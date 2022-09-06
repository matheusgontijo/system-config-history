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
            isLoading: false,
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
            limit: "20",
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

        changePage: {
            get: function() {
                return this.page;
            },
            set: function(v) {
                return this.page = v;
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
    },

    methods: {
        async createdComponent() {
            this.loadGridData();
        },

        loadGridData() {
            // @TODO: ONLY USE LOADING AFTER FEW SECONDS... BETTER FOR UX

            // this.isLoading = true;

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
                this.isLoading = false;
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

        getPaginationItems() {
            let [leftItems, rightItems] = this.calculatePaginationItems(this.page, 9, this.count, this.limit);

            let paginationItems = [];

            paginationItems.push({
                page: this.page,
                current: true,
            });

            for (let i = 1; i <= rightItems; i++) {
                paginationItems.push({
                    page: i,
                    current: false,
                });
            }

            console.log(paginationItems)






            return paginationItems;
        },

        calculatePaginationItems(currentPage, maxPaginationItems, count, limit) {
            let totalPages = Math.ceil(count / limit);

            return this.calculatePaginationItemsInternal(
                currentPage,
                totalPages,
                maxPaginationItems,
                0,
                0,
                'right',
                0
            );
        },

        calculatePaginationItemsInternal(
            currentPage,
            totalPages,
            maxPaginationItems,
            leftItems,
            rightItems,
            lastAddedPosition
        ) {
            // console.log('jjj:' + jjj);
            // if (jjj > 100) {
            //     return;
            // }

            // // @TODO: REMOVE IT
            // console.log('currentPage:' + currentPage);
            // console.log('totalPages:' + totalPages);
            // console.log('maxPaginationItems:' + maxPaginationItems);
            // console.log('leftItems:' + leftItems);
            // console.log('rightItems:' + rightItems);
            // console.log('lastAddedPosition:' + lastAddedPosition);
            // console.log('--------------------');
            // console.log('--------------------');
            // console.log('--------------------');
            // console.log('--------------------');

            let totalItemsAdded = leftItems + rightItems;

            if (totalItemsAdded >= maxPaginationItems) {
                return [leftItems, rightItems];
            }

            lastAddedPosition = lastAddedPosition === 'left' ? 'right' : 'left';

            if (lastAddedPosition === 'left' && (currentPage - leftItems - 1) >= 1) {
                leftItems++;
            }

            if (lastAddedPosition === 'right' && (currentPage + rightItems + 1) <= Number.MAX_SAFE_INTEGER) {
                rightItems++;
            }

            return this.calculatePaginationItemsInternal(
                currentPage,
                totalPages,
                maxPaginationItems,
                leftItems,
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
            this.limit = 20;
        },

        formatConfigurationKey(value) {
            const maxCharacters = 40;

            if (value.length <= maxCharacters) {
                return value;
            }

            let valuesSplitted = [];

            for (let i = 0; i < value.length; i += maxCharacters) {
                valuesSplitted.push(value.substring(i, i + maxCharacters));
            }

            return valuesSplitted.join('<br />');
        },
    }
});
