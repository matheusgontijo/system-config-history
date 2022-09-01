<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\Api;

// phpcs:ignore
use MatheusGontijo\SystemConfigHistory\Repository\System\MatheusGontijoSystemConfigHistory\Api\MatheusGontijoSystemConfigHistoryRouteRepository;
use Shopware\Core\Framework\Adapter\Translation\Translator;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\Locale\LocaleEntity;
use Shopware\Core\System\User\UserEntity;
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
        EntityRepositoryInterface $userRepository,
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

        $locale = $this->getLocale($context, $userRepository);

        $defaultSalesChannelName = $translator->trans(
            'matheus-gontijo-system-config-history.historyTab.defaultSalesChannelName',
            [],
            'administration',
            $locale->getCode()
        );

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

    private function getLocale(
        Context $context,
        EntityRepositoryInterface $userRepository
    ): LocaleEntity {
        $source = $context->getSource();
        assert($source instanceof AdminApiSource);

        $userId = $source->getUserId();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $userId));
        $criteria->addAssociation('locale');

        $user = $userRepository->search($criteria, Context::createDefaultContext())->first();
        assert($user instanceof UserEntity);

        $locale = $user->getLocale();
        assert($locale instanceof LocaleEntity);

        return $locale;
    }
}
