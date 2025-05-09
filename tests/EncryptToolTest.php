<?php

namespace Shanla\Tools\Tests;

use PHPUnit\Framework\TestCase;
use Shanla\Tools\EncryptTool;
use Exception;

class EncryptToolTest extends TestCase
{
    private string $testKey;
    private string $testData;
    private array $rsaKeys;

    protected function setUp(): void
    {
        parent::setUp();
        // 生成测试密钥
        $this->testKey = EncryptTool::generateKey();
        $this->testData = 'Hello, World!';
        
        // 生成 RSA 密钥对
        $this->rsaKeys = EncryptTool::generateKeyPair();
    }

    public function testGenerateKey()
    {
        // 测试默认长度
        $key = EncryptTool::generateKey();
        $this->assertNotEmpty($key);
        $this->assertIsString($key);
        
        // 测试自定义长度
        $key = EncryptTool::generateKey(16);
        $this->assertNotEmpty($key);
        $this->assertIsString($key);
        
        // 测试无效长度
        $this->expectException(Exception::class);
        EncryptTool::generateKey(0);
    }

    public function testEncryptAndDecrypt()
    {
        // 测试基本加密解密
        $encrypted = EncryptTool::encrypt($this->testData, $this->testKey);
        $decrypted = EncryptTool::decrypt($encrypted, $this->testKey);
        $this->assertEquals($this->testData, $decrypted);
        
        // 测试不同加密算法
        $encrypted = EncryptTool::encrypt($this->testData, $this->testKey, 'AES-128-CBC');
        $decrypted = EncryptTool::decrypt($encrypted, $this->testKey, 'AES-128-CBC');
        $this->assertEquals($this->testData, $decrypted);
        
        // 测试无效的加密数据
        $this->expectException(Exception::class);
        EncryptTool::decrypt('invalid_base64_data', $this->testKey);
    }

    public function testGenerateKeyPair()
    {
        $keys = EncryptTool::generateKeyPair();
        
        $this->assertArrayHasKey('publicKey', $keys);
        $this->assertArrayHasKey('privateKey', $keys);
        $this->assertNotEmpty($keys['publicKey']);
        $this->assertNotEmpty($keys['privateKey']);
        
        // 测试自定义密钥长度
        $keys = EncryptTool::generateKeyPair(4096);
        $this->assertArrayHasKey('publicKey', $keys);
        $this->assertArrayHasKey('privateKey', $keys);
    }

    public function testEncryptAndDecryptWithRSA()
    {
        // 测试 RSA 加密解密
        $encrypted = EncryptTool::encryptWithPublicKey($this->testData, $this->rsaKeys['publicKey']);
        $decrypted = EncryptTool::decryptWithPrivateKey($encrypted, $this->rsaKeys['privateKey']);
        $this->assertEquals($this->testData, $decrypted);
        
        // 测试无效的公钥
        $this->expectException(Exception::class);
        @EncryptTool::encryptWithPublicKey($this->testData, 'invalid_public_key');
        
        // 测试无效的私钥
        $this->expectException(Exception::class);
        @EncryptTool::decryptWithPrivateKey($encrypted, 'invalid_private_key');
    }

    public function testEncryptAndDecryptFile()
    {
        // 创建测试文件
        $testFile = tempnam(sys_get_temp_dir(), 'test_');
        $encryptedFile = tempnam(sys_get_temp_dir(), 'encrypted_');
        $decryptedFile = tempnam(sys_get_temp_dir(), 'decrypted_');
        
        file_put_contents($testFile, $this->testData);
        
        // 测试文件加密解密
        EncryptTool::encryptFile($testFile, $this->testKey, $encryptedFile);
        EncryptTool::decryptFile($encryptedFile, $this->testKey, $decryptedFile);
        
        $this->assertEquals($this->testData, file_get_contents($decryptedFile));
        
        // 清理测试文件
        unlink($testFile);
        unlink($encryptedFile);
        unlink($decryptedFile);
        
        // 测试不存在的文件
        $this->expectException(Exception::class);
        @EncryptTool::encryptFile('non_existent_file', $this->testKey, $encryptedFile);
    }

    public function testSignAndVerify()
    {
        // 测试签名和验证
        $signature = EncryptTool::sign($this->testData, $this->testKey);
        $this->assertTrue(EncryptTool::verify($this->testData, $signature, $this->testKey));
        
        // 测试不同的哈希算法
        $signature = EncryptTool::sign($this->testData, $this->testKey, 'sha512');
        $this->assertTrue(EncryptTool::verify($this->testData, $signature, $this->testKey, 'sha512'));
        
        // 测试无效的签名
        $this->assertFalse(EncryptTool::verify($this->testData, 'invalid_signature', $this->testKey));
        
        // 测试修改后的数据
        $this->assertFalse(EncryptTool::verify('Modified Data', $signature, $this->testKey));
    }

    public function testSignAndVerifyWithRSA()
    {
        // 测试 RSA 签名和验证
        $signature = EncryptTool::signWithPrivateKey($this->testData, $this->rsaKeys['privateKey']);
        $this->assertTrue(EncryptTool::verifyWithPublicKey($this->testData, $signature, $this->rsaKeys['publicKey']));
        
        // 测试不同的签名算法
        $signature = EncryptTool::signWithPrivateKey($this->testData, $this->rsaKeys['privateKey'], 'sha512');
        $this->assertTrue(EncryptTool::verifyWithPublicKey($this->testData, $signature, $this->rsaKeys['publicKey'], 'sha512'));
        
        // 测试无效的签名
        $this->assertFalse(EncryptTool::verifyWithPublicKey($this->testData, base64_encode('invalid_signature'), $this->rsaKeys['publicKey']));
        
        // 测试修改后的数据
        $this->assertFalse(EncryptTool::verifyWithPublicKey('Modified Data', $signature, $this->rsaKeys['publicKey']));
        
        // 测试无效的私钥
        $this->expectException(Exception::class);
        @EncryptTool::signWithPrivateKey($this->testData, 'invalid_private_key');
        
        // 测试无效的公钥
        $this->expectException(Exception::class);
        @EncryptTool::verifyWithPublicKey($this->testData, $signature, 'invalid_public_key');
    }

    public function testKeyFormatConversion()
    {
        // 测试 PEM 转 PKCS#8
        $pkcs8PrivateKey = EncryptTool::convertToPKCS8($this->rsaKeys['privateKey']);
        $this->assertStringContainsString('PRIVATE KEY', $pkcs8PrivateKey);
        
        $pkcs8PublicKey = EncryptTool::convertToPKCS8($this->rsaKeys['publicKey'], false);
        $this->assertStringContainsString('PUBLIC KEY', $pkcs8PublicKey);
        
        // 测试 PKCS#8 转 PEM
        $pemPrivateKey = EncryptTool::convertToPEM($pkcs8PrivateKey);
        $this->assertStringContainsString('PRIVATE KEY', $pemPrivateKey);
        
        $pemPublicKey = EncryptTool::convertToPEM($pkcs8PublicKey, false);
        $this->assertStringContainsString('PUBLIC KEY', $pemPublicKey);
        
        // 测试无效的密钥格式
        $this->expectException(Exception::class);
        EncryptTool::convertToPKCS8('invalid_key');
        
        $this->expectException(Exception::class);
        EncryptTool::convertToPEM('invalid_key');
    }

    public function testVerify()
    {
        // 测试基本验证
        $signature = EncryptTool::sign($this->testData, $this->testKey);
        $this->assertTrue(EncryptTool::verify($this->testData, $signature, $this->testKey));
        
        // 测试不同的哈希算法
        $signature = EncryptTool::sign($this->testData, $this->testKey, 'sha512');
        $this->assertTrue(EncryptTool::verify($this->testData, $signature, $this->testKey, 'sha512'));
        
        // 测试无效的签名
        $this->assertFalse(EncryptTool::verify($this->testData, 'invalid_signature', $this->testKey));
        
        // 测试修改后的数据
        $this->assertFalse(EncryptTool::verify('Modified Data', $signature, $this->testKey));
        
        // 测试空数据
        $this->assertFalse(EncryptTool::verify('', $signature, $this->testKey));
        
        // 测试空签名
        $this->assertFalse(EncryptTool::verify($this->testData, '', $this->testKey));
        
        // 测试空密钥
        $this->assertFalse(EncryptTool::verify($this->testData, $signature, ''));
    }
} 