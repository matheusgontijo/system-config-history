import template from './matheus-gontijo-system-config-history-view-history.html.twig';
import './matheus-gontijo-system-config-history-view-history.scss';

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
                sales_channel_id: null,
                username: null,
                action_type: null,
            },
            sortBy: 'created_at',
            sortDirection: 'DESC',
            page: 1,
            limit: 20,
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

        filterSalesChannelId: {
            get: function() {
                return this.filters.sales_channel_id;
            },
            set: function(v) {
                return this.filters.sales_channel_id = v;
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
    },

    methods: {
        async createdComponent() {
            this.loadGridData();
        },

        loadGridData() {
            this.isLoading = true;

            this.MatheusGontijoSystemConfigHistoryViewHistoryService.getRows(
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

        hasPagination() {
            return !this.isLoading && this.count > this.limit;
        },

        getPaginationItems() {
            let maxPaginationItems = 10;
            let totalPages = Math.ceil(this.count / this.limit);

            let paginationItems = this.calculatePaginationItems(
                this.page,
                totalPages,
                maxPaginationItems
            );


        },

        calculatePaginationItems(
            currentPage,
            totalPages,
            maxPaginationItems
        ) {
            return this.calculatePaginationItemsInternal(
                currentPage,
                totalPages,
                maxPaginationItems,
                0,
                0,
                'right'
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
            // @TODO: REMOVE IT
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


            if (totalPages >= maxPaginationItems) {
                return [leftItems, rightItems];
            }

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
        }
    }
});
