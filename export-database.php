<?php

/**
 * Script untuk export database ke file SQL dan membuat ZIP
 * Support: MySQL, SQLite
 */

echo "========================================\n";
echo "Export Database ke File SQL\n";
echo "========================================\n\n";

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$connection = config('database.default');
$dateStamp = date('Ymd-His');
$sqlFileName = "database-backup-{$dateStamp}.sql";
$zipFileName = "database-backup-{$dateStamp}.zip";

echo "Database Connection: {$connection}\n";
echo "SQL File: {$sqlFileName}\n";
echo "ZIP File: {$zipFileName}\n\n";

try {
    $db = DB::connection();
    $pdo = $db->getPdo();
    
    if ($connection === 'sqlite') {
        echo "Exporting SQLite database...\n";
        exportSQLite($sqlFileName);
    } elseif ($connection === 'mysql' || $connection === 'mariadb') {
        echo "Exporting MySQL database...\n";
        exportMySQL($sqlFileName);
    } else {
        die("ERROR: Database connection '{$connection}' tidak didukung.\n");
    }
    
    // Buat ZIP file
    echo "\nMembuat file ZIP...\n";
    createZip($sqlFileName, $zipFileName);
    
    // Hapus file SQL sementara (opsional)
    // unlink($sqlFileName);
    
    $zipSize = filesize($zipFileName) / 1024 / 1024;
    
    echo "\n========================================\n";
    echo "Database berhasil di-export!\n";
    echo "SQL File: {$sqlFileName}\n";
    echo "ZIP File: {$zipFileName}\n";
    echo "Ukuran ZIP: " . round($zipSize, 2) . " MB\n";
    echo "========================================\n";
    
} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "Mencoba export dari SQLite file...\n\n";
    
    // Fallback ke SQLite file
    if (file_exists('database/database.sqlite')) {
        exportSQLiteFile('database/database.sqlite', $sqlFileName);
        createZip($sqlFileName, $zipFileName);
        $zipSize = filesize($zipFileName) / 1024 / 1024;
        
        echo "\n========================================\n";
        echo "Database berhasil di-export dari SQLite file!\n";
        echo "SQL File: {$sqlFileName}\n";
        echo "ZIP File: {$zipFileName}\n";
        echo "Ukuran ZIP: " . round($zipSize, 2) . " MB\n";
        echo "========================================\n";
    } else {
        die("ERROR: Tidak dapat mengakses database dan file SQLite tidak ditemukan.\n");
    }
}

/**
 * Export MySQL database
 */
function exportMySQL($filename) {
    $config = config('database.connections.mysql');
    
    $host = $config['host'];
    $port = $config['port'] ?? 3306;
    $database = $config['database'];
    $username = $config['username'];
    $password = $config['password'];
    
    // Cek apakah mysqldump tersedia
    $mysqldump = 'mysqldump';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows - cek di common locations
        $paths = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp\\bin\\mysql\\mysql' . substr(phpversion(), 0, 3) . '\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\xampp\\mysql\\bin\\mysqldump.exe',
        ];
        
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $mysqldump = $path;
                break;
            }
        }
    }
    
    $command = sprintf(
        '"%s" --host=%s --port=%s --user=%s --password=%s %s > %s 2>&1',
        $mysqldump,
        escapeshellarg($host),
        escapeshellarg($port),
        escapeshellarg($username),
        escapeshellarg($password),
        escapeshellarg($database),
        escapeshellarg($filename)
    );
    
    exec($command, $output, $returnVar);
    
    if ($returnVar !== 0 || !file_exists($filename) || filesize($filename) == 0) {
        // Fallback: export via PHP
        echo "mysqldump tidak tersedia, menggunakan export via PHP...\n";
        exportMySQLViaPHP($filename, $host, $port, $database, $username, $password);
    } else {
        echo "Database berhasil di-export menggunakan mysqldump.\n";
    }
}

/**
 * Export MySQL via PHP (fallback)
 */
function exportMySQLViaPHP($filename, $host, $port, $database, $username, $password) {
    try {
        $pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$database}",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        $sql = "-- MySQL Database Export\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: {$database}\n\n";
        $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $sql .= "SET time_zone = \"+00:00\";\n\n";
        $sql .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
        $sql .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
        $sql .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
        $sql .= "/*!40101 SET NAMES utf8mb4 */;\n\n";
        
        // Get all tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            $sql .= "\n-- Table structure for table `{$table}`\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
            $sql .= $createTable['Create Table'] . ";\n\n";
            
            // Get table data
            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($rows) > 0) {
                $sql .= "-- Dumping data for table `{$table}`\n";
                $sql .= "LOCK TABLES `{$table}` WRITE;\n";
                $sql .= "/*!40000 ALTER TABLE `{$table}` DISABLE KEYS */;\n";
                
                foreach ($rows as $row) {
                    $values = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = $pdo->quote($value);
                        }
                    }
                    $sql .= "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n";
                }
                
                $sql .= "/*!40000 ALTER TABLE `{$table}` ENABLE KEYS */;\n";
                $sql .= "UNLOCK TABLES;\n\n";
            }
        }
        
        $sql .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
        $sql .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
        $sql .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";
        
        file_put_contents($filename, $sql);
        echo "Database berhasil di-export via PHP.\n";
        
    } catch (PDOException $e) {
        throw new Exception("Gagal export MySQL: " . $e->getMessage());
    }
}

/**
 * Export SQLite database
 */
function exportSQLite($filename) {
    $dbPath = config('database.connections.sqlite.database');
    
    if (!file_exists($dbPath)) {
        throw new Exception("File SQLite tidak ditemukan: {$dbPath}");
    }
    
    // Cek apakah sqlite3 command tersedia
    $sqlite3 = 'sqlite3';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $sqlite3 = 'sqlite3.exe';
    }
    
    $command = sprintf(
        '"%s" "%s" .dump > "%s" 2>&1',
        $sqlite3,
        escapeshellarg($dbPath),
        escapeshellarg($filename)
    );
    
    exec($command, $output, $returnVar);
    
    if ($returnVar !== 0 || !file_exists($filename) || filesize($filename) == 0) {
        // Fallback: export via PHP
        echo "sqlite3 command tidak tersedia, menggunakan export via PHP...\n";
        exportSQLiteFile($dbPath, $filename);
    } else {
        echo "Database berhasil di-export menggunakan sqlite3.\n";
    }
}

/**
 * Export SQLite file via PHP
 */
function exportSQLiteFile($dbPath, $filename) {
    try {
        $pdo = new PDO("sqlite:{$dbPath}");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "-- SQLite Database Export\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: {$dbPath}\n\n";
        
        // Get all tables
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            $sql .= "\n-- Table structure for table `{$table}`\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            $createTable = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='{$table}'")->fetchColumn();
            $sql .= $createTable . ";\n\n";
            
            // Get table data
            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($rows) > 0) {
                $sql .= "-- Dumping data for table `{$table}`\n";
                
                foreach ($rows as $row) {
                    $columns = array_keys($row);
                    $values = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = $pdo->quote($value);
                        }
                    }
                    $sql .= "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }
        
        file_put_contents($filename, $sql);
        echo "Database berhasil di-export via PHP.\n";
        
    } catch (PDOException $e) {
        throw new Exception("Gagal export SQLite: " . $e->getMessage());
    }
}

/**
 * Create ZIP file
 */
function createZip($sqlFile, $zipFile) {
    if (!extension_loaded('zip')) {
        die("ERROR: Extension ZIP tidak tersedia.\n");
    }
    
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        die("ERROR: Tidak dapat membuat file ZIP.\n");
    }
    
    if (file_exists($sqlFile)) {
        $zip->addFile($sqlFile, basename($sqlFile));
        echo "File SQL ditambahkan ke ZIP.\n";
    }
    
    // Tambahkan file SQLite jika ada
    if (file_exists('database/database.sqlite')) {
        $zip->addFile('database/database.sqlite', 'database.sqlite');
        echo "File SQLite ditambahkan ke ZIP.\n";
    }
    
    $zip->close();
    echo "File ZIP berhasil dibuat.\n";
}

