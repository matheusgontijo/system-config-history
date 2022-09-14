import template from './matheus-gontijo-system-config-history-view-history.html.twig';
import './matheus-gontijo-system-config-history-view-history.scss';
const format = Shopware.Utils.format;

/**
 * @private
 */
Shopware.Component.register('matheus-gontijo-system-config-history-view-history', {
    template,

    inject: ['MatheusGontijoSystemConfigHistoryViewHistoryService'],

    mixins: ['notification'],

    data() {
        return {
            isLoading: false,
            isLoadingSpin: false,
            filters: {
                configuration_key: null,
                configuration_value_old: null,
                configuration_value_new: null,
                sales_channel_name: null,
                username: null,
                created_at: null,
            },
            sortBy: 'created_at',
            sortDirection: 'DESC',
            page: 1,
            limit: '50',
            count: 0,
            rows: [],
            informationModalId: null,
            informationModalData: null,
            warningModal: false,
        };
    },

    created() {
        this.createdComponent();
    },

    watch: {
        filters: {
            handler(v){
                this.loadGridData();
            },
            deep: true
        },

        sortBy() {
            this.loadGridData();
        },

        sortDirection() {
            this.loadGridData();
        },

        page() {
            this.loadGridData();
        },

        limit() {
            this.loadGridData();
        },
    },

    computed: {
        filterConfigurationKey: {
            get: function() {
                return this.filters.configuration_key;
            },
            set: function(v) {
                this.page = 1;
                return this.filters.configuration_key = v;
            }
        },

        filterConfigurationValueOld: {
            get: function() {
                return this.filters.configuration_value_old;
            },
            set: function(v) {
                this.page = 1;
                return this.filters.configuration_value_old = v;
            }
        },

        filterConfigurationValueNew: {
            get: function() {
                return this.filters.configuration_value_new;
            },
            set: function(v) {
                this.page = 1;
                return this.filters.configuration_value_new = v;
            }
        },

        filterSalesChannelName: {
            get: function() {
                return this.filters.sales_channel_name;
            },
            set: function(v) {
                this.page = 1;
                return this.filters.sales_channel_name = v;
            }
        },

        filterUsername: {
            get: function() {
                return this.filters.username;
            },
            set: function(v) {
                this.page = 1;
                return this.filters.username = v;
            }
        },

        filterCreatedAt: {
            get: function() {
                return this.filters.created_at;
            },
            set: function(v) {
                this.page = 1;
                return this.filters.created_at = v;
            }
        },

        selectLimit: {
            get: function() {
                return this.limit;
            },
            set: function(v) {
                this.page = 1;
                return this.limit = v;
            }
        },

        totalRecords: {
            get: function() {
                return this.count;
            },
            set: function(v) {
                return this.count = v;
            }
        },

        limitOptions() {
            return [
                20,
                50,
                100,
                250,
                500,
            ];
        },

        hasPagination() {
            return this.count > this.limit;
        },

        getPaginationItems() {
            let [leftItems, rightItems] = this.calculatePaginationItemsMethod(this.page, 9, this.count, this.limit);

            let paginationItems = [];

            for (let i = leftItems; i >= 1; i--) {
                paginationItems.push({
                    page: this.page - i,
                    current: false,
                });
            }

            paginationItems.push({
                page: this.page,
                current: true,
            });

            for (let i = 1; i <= rightItems; i++) {
                paginationItems.push({
                    page: this.page + i,
                    current: false,
                });
            }

            return paginationItems;
        },

        showInformationModal() {
            return this.informationModalId !== null && !this.warningModal;
        },

        showInformationModalData() {
            return this.informationModalData !== null;
        },

        showWarningModal() {
            return this.warningModal;
        },
    },

    methods: {
        async createdComponent() {
            this.loadGridData();
        },

        loadGridData() {
            this.isLoadingSpin = true;

            let localeCode = Shopware.Application.getContainer('factory').locale.getLastKnownLocale();
            let defaultSalesChannelName = this.$tc(this.transPrefix('grid.defaultSalesChannelName'));

            this.MatheusGontijoSystemConfigHistoryViewHistoryService.getRows(
                localeCode,
                defaultSalesChannelName,
                this.filters,
                this.sortBy,
                this.sortDirection,
                this.page,
                this.limit
            ).then((response) => {
                this.count = response.count;
                this.rows = response.rows;
            }).catch(() => {
                this.createNotificationError({
                    message: this.$tc(this.transPrefix('grid.errorLoadingGrid')),
                });
            }).finally(() => {
                this.isLoadingSpin = false;
            });
        },

        changeSortByAndSortDirection(sortBy) {
            this.page = 1;

            if (sortBy === this.sortBy) {
                if (this.sortDirection === 'ASC') {
                    this.sortDirection = 'DESC';
                    return;
                }

                this.sortDirection = 'ASC';

                return;
            }

            this.sortBy = sortBy;
            this.sortDirection = 'ASC';
        },

        sortDirectionArrow(sortBy) {
            if (this.sortBy !== sortBy) {
                return '';
            }

            if (this.sortDirection === 'ASC') {
                return '⬆';
            }

            return '⬇';
        },

        calculatePaginationItemsMethod(currentPage, maxPaginationItems, count, limit) {
            let totalPages = Math.ceil(count / limit);

            return this.calculatePaginationItems(
                currentPage,
                totalPages,
                maxPaginationItems,
                false,
                0,
                false,
                0,
                'right'
            );
        },

        calculatePaginationItems(
            currentPage,
            totalPages,
            maxPaginationItems,
            leftCompleted,
            leftItems,
            rightCompleted,
            rightItems,
            lastAddedPosition
        ) {
            let totalItemsAdded = leftItems + rightItems;

            if (totalItemsAdded >= maxPaginationItems) {
                return [leftItems, rightItems];
            }

            if ((currentPage - leftItems - 1) < 1) {
                leftCompleted = true;
            }

            if ((currentPage + rightItems + 1) > totalPages) {
                rightCompleted = true;
            }

            if (leftCompleted && rightCompleted) {
                return [leftItems, rightItems];
            }

            lastAddedPosition = lastAddedPosition === 'left' ? 'right' : 'left';

            if (lastAddedPosition === 'left' && leftCompleted === false) {
                leftItems++;
            }

            if (lastAddedPosition === 'right' && rightCompleted === false) {
                rightItems++;
            }

            return this.calculatePaginationItems(
                currentPage,
                totalPages,
                maxPaginationItems,
                leftCompleted,
                leftItems,
                rightCompleted,
                rightItems,
                lastAddedPosition
            );
        },

        pagePrefix(value) {
            if (value === undefined) {
                return 'matheus-gontijo-system-config-history-view-history';
            }

            return 'matheus-gontijo-system-config-history-view-history-' + value;
        },

        transPrefix(value) {
            return 'matheus-gontijo-system-config-history-config.historyTab.' + value;
        },

        refreshGrid() {
            this.loadGridData();
        },

        resetGrid() {
            this.filters.configuration_key = null;
            this.filters.configuration_value_old = null;
            this.filters.configuration_value_new = null;
            this.filters.sales_channel_name = null;
            this.filters.username = null;
            this.filters.created_at = null;
            this.sortBy = 'created_at';
            this.sortDirection = 'DESC';
            this.page = 1;
            this.limit = 50;
        },

        formatConfigurationKey(value) {
            const maxCharacters = 50;

            if (value.length <= maxCharacters) {
                return value;
            }

            let valuesSplitted = [];

            for (let i = 0; i < value.length; i += maxCharacters) {
                valuesSplitted.push(value.substring(i, i + maxCharacters));
            }

            return valuesSplitted.join('<br />');
        },

        changePage(page) {
            this.page = page;

            const element = document.getElementsByClassName('sw-tabs__content')[0];
            element.scrollIntoView({behavior: "smooth"});
        },

        getPaginationItemClass(paginationItem) {
            let paginationClass = this.pagePrefix('pagination-item');

            if (paginationItem.current) {
                return paginationClass + ' ' + this.pagePrefix('pagination-item-current');
            }

            return paginationClass;
        },

        openInformationModal(id) {
            this.informationModalId = id;
            this.informationModalData = null;
            this.loadInformationModalData();
        },

        closeInformationModal() {
            this.informationModalId = null;
        },

        openWarningModal() {
            this.warningModal = true;
        },

        closeWarningModal() {
            this.warningModal = false;
        },

        loadInformationModalData() {
            let localeCode = Shopware.Application.getContainer('factory').locale.getLastKnownLocale();
            let defaultSalesChannelName = this.$tc(this.transPrefix('grid.defaultSalesChannelName'));

            this.MatheusGontijoSystemConfigHistoryViewHistoryService.getModalData(
                localeCode,
                defaultSalesChannelName,
                this.informationModalId
            ).then((response) => {
                this.informationModalData = response;
            }).catch(() => {
                this.informationModalId = null;
                this.createNotificationError({
                    message: this.$tc(this.transPrefix('grid.errorLoadingModal')),
                });
            });
        },
    }
});
