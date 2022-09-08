import template from './matheus-gontijo-system-config-history-view-history.html.twig';
import './matheus-gontijo-system-config-history-view-history.scss';
const format = Shopware.Utils.format;

/**
 * @private
 */
Shopware.Component.register('matheus-gontijo-system-config-history-view-history', {
    template,

    inject: ['MatheusGontijoSystemConfigHistoryViewHistoryService'],

    data() {
        return {
            isLoadingSpin: false,
            filters: {
                configuration_key: null,
                configuration_value_old: null,
                configuration_value_new: null,
                sales_channel_name: null,
                username: null,
            },
            sortBy: 'created_at',
            sortDirection: 'DESC',
            page: 1,
            limit: '50',
            count: 0,
            rows: [],
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
                return this.filters.configuration_key = v;
            }
        },

        filterConfigurationValueOld: {
            get: function() {
                return this.filters.configuration_value_old;
            },
            set: function(v) {
                return this.filters.configuration_value_old = v;
            }
        },

        filterConfigurationValueNew: {
            get: function() {
                return this.filters.configuration_value_new;
            },
            set: function(v) {
                return this.filters.configuration_value_new = v;
            }
        },

        filterSalesChannelName: {
            get: function() {
                return this.filters.sales_channel_name;
            },
            set: function(v) {
                return this.filters.sales_channel_name = v;
            }
        },

        filterUsername: {
            get: function() {
                return this.filters.username;
            },
            set: function(v) {
                return this.filters.username = v;
            }
        },

        selectLimit: {
            get: function() {
                return this.limit;
            },
            set: function(v) {
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
            ];
        },

        hasPagination() {
            return this.count > this.limit;
        },

        getPaginationItems() {
            let [leftItems, rightItems] = this.calculatePaginationItemsMethod(this.page, 9, this.count, this.limit);

            let paginationItems = [];

            let jjj = 0;

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
    },

    methods: {
        async createdComponent() {
            this.loadGridData();
        },

        loadGridData() {
            this.isLoadingSpin = true;

            let defaultSalesChannelName = this.$tc(this.transPrefix('grid.defaultSalesChannelName'));
            let localeCode = Shopware.Application.getContainer('factory').locale.getLastKnownLocale();

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
                this.isLoadingSpin = false;
                // @TODO: ADD FINALLY HERE? IN CASE OF ERROR
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

        columnFormatDate(date) {
            /**
             * @TODO: TEST THIS ON AMERICAN FORMAT (en-US)... to see if it will really change the format accordindly
             */

            const options = { year: 'numeric', month: 'numeric', day: 'numeric', hour: 'numeric', minute: '2-digit' };
            return format.date(date, options);
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
        }
    }
});
