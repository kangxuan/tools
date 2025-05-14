<?php
declare(strict_types = 1);

namespace Shanla\Tools;


use Exception;
use InvalidArgumentException;

class EncryptTool
{
    /**
     * 使用 OpenSSL 加密字符串
     * @param string $data 要加密的数据
     * @param string $key 加密密钥
     * @param string $cipher 加密算法（默认 AES-256-CBC）
     * @return string 返回加密后的字符串，经过 Base64 编码
     * @throws Exception
     */
    public static function encrypt(string $data, string $key, string $cipher = 'AES-256-CBC') : string
    {
        $iv = random_bytes(openssl_cipher_iv_length($cipher));
        $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);

        if ($encrypted === false) {
            throw new Exception('加密失败');
        }

        return base64_encode($iv . $encrypted);
    }

    /**
     * 使用 OpenSSL 解密字符串
     * @param string $data 加密的数据（Base64 编码）
     * @param string $key 加密密钥
     * @param string $cipher 加密算法（默认 AES-256-CBC）
     * @return string 返回解密后的字符串
     * @throws Exception
     */
    public static function decrypt(string $data, string $key, string $cipher = 'AES-256-CBC') : string
    {
        $decoded = base64_decode($data, true);

        if ($decoded === false) {
            throw new InvalidArgumentException('无效的 Base64 编码数据');
        }

        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($decoded, 0, $ivLength);
        $encrypted = substr($decoded, $ivLength);

        $decrypted = openssl_decrypt($encrypted, $cipher, $key, 0, $iv);

        if ($decrypted === false) {
            throw new Exception('解密失败');
        }

        return $decrypted;
    }

    /**
     * 生成一个随机加密密钥
     * @param int $length 密钥长度（字节数）
     * @return string 返回生成的密钥，经过 Base64 编码
     * @throws Exception
     */
    public static function generateKey(int $length = 32) : string
    {
        if ($length <= 0) {
            throw new InvalidArgumentException('密钥长度必须大于零');
        }

        return base64_encode(random_bytes($length));
    }

    /**
     * 使用 RSA 公钥加密数据 -- 适用于短字符串
     * @param string $data 要加密的数据
     * @param string $publicKey 公钥
     * @return string 返回加密后的字符串，经过 Base64 编码
     * @throws Exception
     */
    public static function encryptWithPublicKey(string $data, string $publicKey): string
    {
        $encrypted = '';

        if (!openssl_public_encrypt($data, $encrypted, $publicKey)) {
            throw new Exception('公钥加密失败：' . openssl_error_string());
        }

        return base64_encode($encrypted);
    }

    /**
     * 使用 RSA 私钥解密数据 -- 适用于短字符串
     * @param string $data 加密的数据（Base64 编码）
     * @param string $privateKey 私钥
     * @return string 返回解密后的字符串
     * @throws Exception
     */
    public static function decryptWithPrivateKey(string $data, string $privateKey): string
    {
        $decoded = base64_decode($data, true);

        if ($decoded === false) {
            throw new Exception('无效的 Base64 编码数据。');
        }

        $decrypted = '';

        if (!openssl_private_decrypt($decoded, $decrypted, $privateKey)) {
            throw new Exception('私钥解密失败：' . openssl_error_string());
        }

        return $decrypted;
    }

    /**
     * 生成一对公钥和私钥
     * @param int $keySize 密钥长度（默认 2048 位）
     * @return array 返回包含 'publicKey' 和 'privateKey' 的数组
     * @throws Exception
     */
    public static function generateKeyPair(int $keySize = 2048): array
    {
        $config = [
            'private_key_bits' => $keySize,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $resource = openssl_pkey_new($config);

        if ($resource === false) {
            throw new Exception('密钥对生成失败：' . openssl_error_string());
        }

        openssl_pkey_export($resource, $privateKey);
        $details = openssl_pkey_get_details($resource);

        if ($details === false) {
            throw new Exception('获取公钥详情失败：' . openssl_error_string());
        }

        $publicKey = $details['key'];

        return [
            'publicKey' => $publicKey,
            'privateKey' => $privateKey,
        ];
    }

    /**
     * 加密文件内容并保存到新文件
     * @param string $filePath 原始文件路径
     * @param string $key 加密密钥
     * @param string $outputPath 输出加密文件路径
     * @param string $cipher 加密算法（默认 AES-256-CBC）
     * @throws Exception
     */
    public static function encryptFile(string $filePath, string $key, string $outputPath, string $cipher = 'AES-256-CBC'): void
    {
        $iv = random_bytes(openssl_cipher_iv_length($cipher));
        $data = file_get_contents($filePath);

        if ($data === false) {
            throw new Exception('无法读取文件内容');
        }

        $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);

        if ($encrypted === false) {
            throw new Exception('文件加密失败');
        }

        $result = file_put_contents($outputPath, $iv . $encrypted);

        if ($result === false) {
            throw new Exception('无法写入加密文件。');
        }
    }

    /**
     * 解密文件内容并保存到新文件
     * @param string $filePath 加密文件路径
     * @param string $key 解密密钥
     * @param string $outputPath 输出解密文件路径
     * @param string $cipher 加密算法（默认 AES-256-CBC）
     * @throws Exception
     */
    public static function decryptFile(string $filePath, string $key, string $outputPath, string $cipher = 'AES-256-CBC'): void
    {
        $data = file_get_contents($filePath);

        if ($data === false) {
            throw new Exception('无法读取加密文件内容');
        }

        // 从加密数据中提取 IV 和密文
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        // 解密文件内容
        $decrypted = openssl_decrypt($encrypted, $cipher, $key, 0, $iv);

        if ($decrypted === false) {
            throw new Exception('文件解密失败');
        }

        // 将解密后的数据写入输出文件
        $result = file_put_contents($outputPath, $decrypted);

        if ($result === false) {
            throw new Exception('无法写入解密文件');
        }
    }


    /**
     * 使用哈希算法生成字符串的签名
     * @param string $data 要签名的数据
     * @param string $key 签名密钥
     * @param string $algorithm 哈希算法（默认 sha256）
     * @return string 返回签名结果，经过 Base64 编码
     */
    public static function sign(string $data, string $key, string $algorithm = 'sha256') : string
    {
        return base64_encode(hash_hmac($algorithm, $data, $key, true));
    }

    /**
     * 验证签名是否正确
     * @param string $data 要验证的数据
     * @param string $signature 提供的签名（Base64 编码）
     * @param string $key 签名密钥
     * @param string $algorithm 哈希算法（默认 sha256）
     * @return bool 返回签名是否有效
     */
    public static function verify(string $data, string $signature, string $key, string $algorithm = 'sha256') : bool
    {
        $calculatedSignature = self::sign($data, $key, $algorithm);

        return hash_equals($calculatedSignature, $signature);
    }

    /**
     * 使用 RSA 私钥签名数据
     * @param string $data 要签名的数据
     * @param string $privateKey 私钥
     * @param string $algorithm 签名算法（默认 sha256）
     * @return string 返回签名结果，经过 Base64 编码
     * @throws Exception
     */
    public static function signWithPrivateKey(string $data, string $privateKey, string $algorithm = 'sha256'): string
    {
        $signature = '';

        if (!openssl_sign($data, $signature, $privateKey, $algorithm)) {
            throw new Exception('签名失败：' . openssl_error_string());
        }

        return base64_encode($signature);
    }

    /**
     * 使用 RSA 公钥验证签名
     * @param string $data 原始数据
     * @param string $signature 签名（Base64 编码）
     * @param string $publicKey 公钥
     * @param string $algorithm 签名算法（默认 sha256）
     * @return bool 返回签名是否有效
     * @throws Exception
     */
    public static function verifyWithPublicKey(string $data, string $signature, string $publicKey, string $algorithm = 'sha256'): bool
    {
        $decodedSignature = base64_decode($signature, true);

        if ($decodedSignature === false) {
            throw new Exception('无效的签名格式');
        }

        $result = openssl_verify($data, $decodedSignature, $publicKey, $algorithm);
        if ($result === -1) {
            throw new Exception('验证签名时发生错误：' . openssl_error_string());
        }

        return $result === 1;
    }

    /**
     * 将 PEM 格式的密钥转换为 PKCS#8 格式
     * @param string $key PEM 格式的密钥
     * @param bool $isPrivate 是否为私钥
     * @return string 返回 PKCS#8 格式的密钥
     * @throws Exception
     */
    public static function convertToPKCS8(string $key, bool $isPrivate = true): string
    {
        if ($isPrivate) {
            $resource = openssl_pkey_get_private($key);
            if ($resource === false) {
                throw new Exception('无效的私钥：' . openssl_error_string());
            }
            
            $pkcs8Key = '';
            if (!openssl_pkey_export($resource, $pkcs8Key)) {
                throw new Exception('私钥转换失败：' . openssl_error_string());
            }
            
            return $pkcs8Key;
        } else {
            $resource = openssl_pkey_get_public($key);
            if ($resource === false) {
                throw new Exception('无效的公钥：' . openssl_error_string());
            }
            
            $details = openssl_pkey_get_details($resource);
            if ($details === false) {
                throw new Exception('获取公钥详情失败：' . openssl_error_string());
            }
            
            return $details['key'];
        }
    }

    /**
     * 将 PKCS#8 格式的密钥转换为 PEM 格式
     * @param string $key PKCS#8 格式的密钥
     * @param bool $isPrivate 是否为私钥
     * @return string 返回 PEM 格式的密钥
     * @throws Exception
     */
    public static function convertToPEM(string $key, bool $isPrivate = true): string
    {
        if ($isPrivate) {
            $resource = openssl_pkey_get_private($key);
            if ($resource === false) {
                throw new Exception('无效的私钥：' . openssl_error_string());
            }
            
            $pemKey = '';
            if (!openssl_pkey_export($resource, $pemKey)) {
                throw new Exception('私钥转换失败：' . openssl_error_string());
            }
            
            return $pemKey;
        } else {
            $resource = openssl_pkey_get_public($key);
            if ($resource === false) {
                throw new Exception('无效的公钥：' . openssl_error_string());
            }
            
            $details = openssl_pkey_get_details($resource);
            if ($details === false) {
                throw new Exception('获取公钥详情失败：' . openssl_error_string());
            }
            
            return $details['key'];
        }
    }
}