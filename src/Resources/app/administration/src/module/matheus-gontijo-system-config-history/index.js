import MatheusGontijoSystemConfigHistoryViewHistoryService from './service/matheus-gontijo-system-config-history-view-history.service';
import iconComponents from '../../app/assets/icons/icons';

import './components/matheus-gontijo-system-config-history-icon';
import './page/matheus-gontijo-system-config-history';
import './view/matheus-gontijo-system-config-history-view-instances';
import './view/matheus-gontijo-system-config-history-view-history';

Shopware.Service().register('MatheusGontijoSystemConfigHistoryViewHistoryService', () => {
    return new MatheusGontijoSystemConfigHistoryViewHistoryService(
        Shopware.Application.getContainer('init').httpClient,
        Shopware.Service('loginService'),
    );
});

iconComponents.map((component) => {
    return Shopware.Component.register(component.name, component);
});

Shopware.Module.register('matheus-gontijo-system-config-history', {
    type: 'plugin',
    name: 'MatheusGontijoSystemConfigHistory',
    title: 'matheus-gontijo-system-config-history.general.mainMenuItemGeneral',
    description: 'matheus-gontijo-system-config-history.general.descriptionTextModule',
    color: '#ff68b4',
    icon: 'default-symbol-content',
    favicon: 'icon-module-content.png',

    routes: {
        index: {
            component: 'matheus-gontijo-system-config-history',
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index',
                privilege: 'system.core_update',
            },

            redirect: {
                name: 'matheus.gontijo.system.config.history.index.history',
            },

            children: {
                history: {
                    component: 'matheus-gontijo-system-config-history-view-history',
                    path: 'history',
                    meta: {
                        parentPath: 'sw.settings.index',
                        privilege: 'system.core_update',
                    },
                },
                instances: {
                    component: 'matheus-gontijo-system-config-history-view-instances',
                    path: 'instances',
                    meta: {
                        parentPath: 'sw.settings.index',
                        privilege: 'system.core_update',
                    },
                },
            },
        },
    },

    settingsItem: [{
        group: 'system',
        to: 'matheus.gontijo.system.config.history.index',
        label: 'matheus-gontijo-system-config-history.general.mainMenuItemGeneral',
        iconComponent: 'matheus-gontijo-system-config-history-icon',
        backgroundEnabled: true,
    }]
});
