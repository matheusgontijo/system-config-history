<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\Api;

// phpcs:ignore
use MatheusGontijo\SystemConfigHistory\Repository\System\MatheusGontijoSystemConfigHistory\Api\MatheusGontijoSystemConfigHistoryRouteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
class MatheusGontijoSystemConfigHistoryRoute extends AbstractController
{
    /**
     * @TODO: UPDATE >>> break down into multiple lines the method annotations
     * @TODO: UPDATE >>> defaults={"_acl"={"system_config:read"}}
     */

    /**
     * @Since("6.0.0.0")
     * @Route(
     *     "/api/_action/matheus-gontijo/matheus-gontijo-system-config-history/rows",
     *     name="api.action.core.matheus-gontijo.matheus-gontijo-system-config-history.rows",
     *     methods={"POST"},
     *     defaults={"_acl"={"system_config:read"}}
     * )
     */
    public function matheusGontijoSystemConfigHistoryList(
        Request $request,
        MatheusGontijoSystemConfigHistoryRouteRepository $matheusGontijoSystemConfigHistoryRouteRepository
    ): JsonResponse {
        $filters = $request->request->all()['filters'] ?? [];
        \assert(\is_array($filters));

        $sortBy = $request->request->get('sortBy');
        \assert(\is_string($sortBy));

        $sortDirection = $request->request->get('sortDirection');
        \assert(\is_string($sortDirection));

        $page = $request->request->get('page');
        \assert(\is_int($page));

        $limit = $request->request->get('limit');
        \assert(\is_int($limit));

        $count = $matheusGontijoSystemConfigHistoryRouteRepository->getCount($filters);

        $rows = $matheusGontijoSystemConfigHistoryRouteRepository->getRows(
            $filters,
            $sortBy,
            $sortDirection,
            $page,
            $limit
        );

        $data = [
            'count' => $count,
            'rows' => $rows,
        ];

        return new JsonResponse($data);
    }
}
