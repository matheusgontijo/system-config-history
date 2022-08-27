import template from './matheus-gontijo-system-config-history.html.twig';
import './matheus-gontijo-system-config-history.scss';

/**
 * @private
 */
Shopware.Component.register('matheus-gontijo-system-config-history', {
    template,

    inject: ['repositoryFactory'],

    data() {
        return {};
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    methods: {
        onChangeLanguage() {
            if (this.$refs.tabContent.reloadContent) {
                this.$refs.tabContent.reloadContent();
            }
        },
    },
});
