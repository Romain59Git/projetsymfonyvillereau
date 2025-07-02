<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HealthController extends AbstractController
{
    #[Route('/health', name: 'app_health', methods: ['GET'])]
    public function health(Connection $connection): JsonResponse
    {
        $checks = [
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'environment' => $this->getParameter('kernel.environment'),
            'checks' => []
        ];

        // Database check
        try {
            $connection->executeQuery('SELECT 1');
            $checks['checks']['database'] = 'ok';
        } catch (\Exception $e) {
            $checks['checks']['database'] = 'error';
            $checks['status'] = 'error';
        }

        // Disk space check
        $diskFree = disk_free_space('/');
        $diskTotal = disk_total_space('/');
        $diskUsagePercent = round((($diskTotal - $diskFree) / $diskTotal) * 100, 2);
        
        $checks['checks']['disk_usage'] = [
            'status' => $diskUsagePercent < 90 ? 'ok' : 'warning',
            'usage_percent' => $diskUsagePercent
        ];

        // Memory check
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        $checks['checks']['memory'] = [
            'usage_mb' => round($memoryUsage / 1024 / 1024, 2),
            'limit' => $memoryLimit
        ];

        $httpCode = $checks['status'] === 'ok' ? 200 : 503;
        
        return new JsonResponse($checks, $httpCode);
    }

    #[Route('/health/simple', name: 'app_health_simple', methods: ['GET'])]
    public function healthSimple(): JsonResponse
    {
        return new JsonResponse(['status' => 'healthy']);
    }
} 