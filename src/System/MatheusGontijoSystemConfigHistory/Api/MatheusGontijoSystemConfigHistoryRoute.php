<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\Api;

// phpcs:ignore
use MatheusGontijo\SystemConfigHistory\Repository\System\MatheusGontijoSystemConfigHistory\Api\MatheusGontijoSystemConfigHistoryRouteRepository;
use Shopware\Core\Framework\Adapter\Translation\Translator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Routing\Annotation\Since;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Contracts\Translation\TranslatorInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

/**
 * @RouteScope(scopes={"api"})
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
        Context $context,
        TranslatorInterface $translator,
        EntityRepositoryInterface $languageRepository,
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

        $defaultSalesChannelName = $this->getDefaultSalesChannelName($context, $languageRepository, $translator);

        $count = $matheusGontijoSystemConfigHistoryRouteRepository->getCount($defaultSalesChannelName, $filters);

        $rows = $matheusGontijoSystemConfigHistoryRouteRepository->getRows(
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

    private function getDefaultSalesChannelName(
        Context $context,
        EntityRepositoryInterface $languageRepository,
        TranslatorInterface $translator
    ): string {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $context->getLanguageId()));
        $criteria->addAssociation('locale');
        $language = $languageRepository->search($criteria, Context::createDefaultContext())->first();

        $localeCode = $language->getLocale()->getCode();

        return $translator->trans(
            'matheus-gontijo-system-config-history.historyTab.defaultSalesChannelName',
            [],
            'administration',
            $localeCode
        );
    }
}
