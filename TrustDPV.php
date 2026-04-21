<?php
/**
 * TrustDPV PHP SDK
 * 
 * Usage:
 *   $tdpv = new TrustDPV('tdpv_live_your_key_here');
 *   $result = $tdpv->verify('username');
 *   $batch = $tdpv->verifyBatch(['user1', 'user2']);
 */

class TrustDPV
{
    private $apiKey;
    private $baseUrl;

    public function __construct(string $apiKey, string $baseUrl = 'https://trustdpv.com')
    {
        if (empty($apiKey)) {
            throw new InvalidArgumentException('apiKey is required');
        }
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    private function request(string $path, array $options = []): array
    {
        $url = $this->baseUrl . $path;
        $headers = [
            'X-API-Key: ' . $this->apiKey,
            'Accept: application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        if (!empty($options['body'])) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['body']);
            $headers[] = 'Content-Type: application/json';
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);
        if ($httpCode >= 400) {
            throw new RuntimeException($data['error'] ?? "API error {$httpCode}", $httpCode);
        }

        return $data;
    }

    /** Lightweight trust verification */
    public function verify(string $username): array
    {
        return $this->request('/api/v1/verify/' . urlencode($username));
    }

    /** Full public profile */
    public function profile(string $username): array
    {
        return $this->request('/api/v1/public/' . urlencode($username));
    }

    /** Batch verify up to 50 usernames */
    public function verifyBatch(array $usernames): array
    {
        if (count($usernames) > 50) {
            throw new InvalidArgumentException('Maximum 50 usernames per batch');
        }
        return $this->request('/api/v1/verify-batch', [
            'body' => json_encode(['usernames' => $usernames]),
        ]);
    }

    /** Get badge SVG URL */
    public function badgeUrl(string $username): string
    {
        return $this->baseUrl . '/badge/' . urlencode($username) . '.svg';
    }

    /** Get public profile URL */
    public function profileUrl(string $username): string
    {
        return $this->baseUrl . '/u/' . urlencode($username);
    }

    /** Health check */
    public function health(): array
    {
        $ch = curl_init($this->baseUrl . '/api/health');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}