<?php
// check_db.php - 检查数据库连接

echo "<h2>检查数据库配置</h2>";

// 尝试不同的配置组合
$configs = [
    // 配置1：你当前的配置
    [
        'name' => '当前配置',
        'host' => 'sql207.infinityfree.com',
        'user' => 'if0_37804247',
        'pass' => 'zw20050311',
        'db' => 'if0_37804247_db_week18'
    ],
    // 配置2：尝试不带数据库名
    [
        'name' => '仅连接（无数据库）',
        'host' => 'sql207.infinityfree.com',
        'user' => 'if0_37804247',
        'pass' => 'zw20050311',
        'db' => null
    ],
    // 配置3：可能的其他数据库名
    [
        'name' => '尝试默认数据库名',
        'host' => 'sql207.infinityfree.com',
        'user' => 'if0_37804247',
        'pass' => 'zw20050311',
        'db' => 'if0_37804247_db'
    ]
];

foreach ($configs as $config) {
    echo "<h3>测试: {$config['name']}</h3>";
    echo "主机: {$config['host']}<br>";
    echo "用户: {$config['user']}<br>";
    
    try {
        $conn = new mysqli(
            $config['host'],
            $config['user'],
            $config['pass'],
            $config['db']
        );
        
        if ($conn->connect_error) {
            echo "<span style='color:red'>❌ 失败: " . $conn->connect_error . "</span><br>";
        } else {
            echo "<span style='color:green'>✅ 连接成功!</span><br>";
            
            // 显示服务器信息
            echo "MySQL版本: " . $conn->server_version . "<br>";
            echo "字符集: " . $conn->character_set_name() . "<br>";
            
            // 如果连接成功但没选数据库，显示所有数据库
            if (!$config['db']) {
                $result = $conn->query("SHOW DATABASES");
                echo "可用数据库:<br>";
                while ($row = $result->fetch_array()) {
                    echo "- " . $row[0] . "<br>";
                }
            } else {
                // 显示当前数据库的表
                $result = $conn->query("SHOW TABLES");
                $tables = [];
                while ($row = $result->fetch_array()) {
                    $tables[] = $row[0];
                }
                echo "数据库 '{$config['db']}' 中的表: " . implode(', ', $tables) . "<br>";
            }
            $conn->close();
        }
    } catch (Exception $e) {
        echo "<span style='color:red'>❌ 异常: " . $e->getMessage() . "</span><br>";
    }
    echo "<hr>";
}
?>