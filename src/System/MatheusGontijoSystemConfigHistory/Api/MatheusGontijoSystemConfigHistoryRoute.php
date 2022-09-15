<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\Api;

use MatheusGontijo\SystemConfigHistory\Model\RevertSystemConfig;
use MatheusGontijo\SystemConfigHistory\Repository\System\MatheusGontijoSystemConfigHistory\Api\MatheusGontijoSystemConfigHistoryRouteRepository; // phpcs:ignore
use MatheusGontijo\SystemConfigHistory\View\Admin\MatheusGontijoSystemConfig\HistoryTab;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Annotation\Since;
use Shopware\Core\System\Locale\LocaleEntity;
use Shopware\Core\System\SalesChannel\NoContentResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 * @psalm-suppress MissingConstructor
 */
class MatheusGontijoSystemConfigHistoryRoute extends AbstractController
{
    /**
     * @Since("6.4.0.0")
     * @Route(
     *     "/api/_action/matheus-gontijo/matheus-gontijo-system-config-history/rows",
     *     name="api.action.core.matheus-gontijo.matheus-gontijo-system-config-history.rows",
     *     methods={"POST"},
     *     defaults={"auth_required"=true, "_acl"={"system_config:read"}}
     * )
     */
    public function matheusGontijoSystemConfigHistoryRows(
        Request $request,
        EntityRepositoryInterface $localeRepository,
        MatheusGontijoSystemConfigHistoryRouteRepository $matheusGontijoSystemConfigHistoryRouteRepository
    ): JsonResponse {
        $filtersRaw = $request->request->all()['filters'] ?? [];
        \assert(\is_array($filtersRaw));

        $filters = [
            'configuration_key' => $filtersRaw['configuration_key'] ?? '',
            'configuration_value_old' => $filtersRaw['configuration_value_old'] ?? '',
            'configuration_value_new' => $filtersRaw['configuration_value_new'] ?? '',
            'sales_channel_name' => $filtersRaw['sales_channel_name'] ?? '',
            'username' => $filtersRaw['username'] ?? '',
            'created_at' => $filtersRaw['created_at'] ?? '',
        ];

        $sortBy = $request->request->get('sortBy');
        \assert(\is_string($sortBy));

        $sortDirection = $request->request->get('sortDirection');
        \assert(\is_string($sortDirection));

        $page = $request->request->get('page');
        \assert(\is_int($page));

        $limit = $request->request->get('limit');
        \assert(\is_int($limit));

        $defaultSalesChannelName = $request->request->get('defaultSalesChannelName');
        \assert(\is_string($defaultSalesChannelName));

        $localeCode = $request->request->get('localeCode');
        \assert(\is_string($localeCode));

        $locale = $this->getLocale($localeCode, $localeRepository);

        $count = $matheusGontijoSystemConfigHistoryRouteRepository->getCount(
            $locale->getId(),
            $defaultSalesChannelName,
            $filters
        );

        $rows = $matheusGontijoSystemConfigHistoryRouteRepository->getRows(
            $locale->getId(),
            $defaultSalesChannelName,
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

    /**
     * @Since("6.4.0.0")
     * @Route(
     *     "/api/_action/matheus-gontijo/matheus-gontijo-system-config-history/modal-data",
     *     name="api.action.core.matheus-gontijo.matheus-gontijo-system-config-history.modata-data",
     *     methods={"POST"},
     *     defaults={"auth_required"=true, "_acl"={"system_config:read"}}
     * )
     */
    public function matheusGontijoSystemConfigHistoryModalData(
        Request $request,
        MatheusGontijoSystemConfigHistoryRouteRepository $matheusGontijoSystemConfigHistoryRouteRepository,
        HistoryTab $historyTab
    ): JsonResponse {
        $defaultSalesChannelName = $request->request->get('defaultSalesChannelName');
        \assert(\is_string($defaultSalesChannelName));

        $matheusGontijoSystemConfigHistoryId = $request->request->get('modalId');
        \assert(\is_string($matheusGontijoSystemConfigHistoryId));

        // phpcs:ignore
        $matheusGontijoSystemConfigHistory = $matheusGontijoSystemConfigHistoryRouteRepository->getMatheusGontijoSystemConfigHistory(
            $matheusGontijoSystemConfigHistoryId
        );

        $modalData = $historyTab->formatModalData($defaultSalesChannelName, $matheusGontijoSystemConfigHistory);

        return new JsonResponse($modalData);
    }

    /**
     * @Since("6.4.0.0")
     * @Route(
     *     "/api/_action/matheus-gontijo/matheus-gontijo-system-config-history/revert-configuration-value",
     *     name="api.action.core.matheus-gontijo.matheus-gontijo-system-config-history.revert-configuration-value",
     *     methods={"POST"},
     *     defaults={"auth_required"=true, "_acl"={"system_config:update"}}
     * )
     */
    public function matheusGontijoSystemConfigHistoryRevertConfigurationValue(
        Request $request,
        RevertSystemConfig $revertSystemConfig
    ): NoContentResponse {
        $matheusGontijoSystemConfigHistoryId = $request->request->get('matheusGontijoSystemConfigHistoryId');
        \assert(\is_string($matheusGontijoSystemConfigHistoryId));

        $configurationValueType = $request->request->get('configurationValueType');
        \assert(\is_string($configurationValueType));

        $revertSystemConfig->revert($matheusGontijoSystemConfigHistoryId, $configurationValueType);

        return new NoContentResponse();
    }

    private function getLocale(string $localeCode, EntityRepositoryInterface $localeRepository): LocaleEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('code', $localeCode));

        $locale = $localeRepository->search($criteria, Context::createDefaultContext())->first();
        \assert($locale instanceof LocaleEntity);

        return $locale;
    }
}
