import ApiService from 'src/core/service/api.service';

/**
 * @private
 */
export default class YotpoConfigService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = '_action/matheus-gontijo') {
        super(httpClient, loginService, apiEndpoint);
    }

    /**
     * @returns {Promise<Object>}
     */
    async getRows(localeCode, defaultSalesChannelName, filters, sortBy, sortDirection, page, limit) {
        const apiRoute = `${this.getApiBasePath()}/matheus-gontijo-system-config-history/rows`;

        limit = parseInt(limit);

        return this.httpClient.post(
            apiRoute,
            {
                localeCode: localeCode,
                defaultSalesChannelName: defaultSalesChannelName,
                filters: filters,
                sortBy: sortBy,
                sortDirection: sortDirection,
                page: page,
                limit: limit
            },
            {
                headers: this.getBasicHeaders(),
            }
        ).then((response) => {
            return ApiService.handleResponse(response);
        });
    }

    /**
     * @returns {Promise<Object>}
     */
    async getModalData(localeCode, defaultSalesChannelName, modalId) {
        const apiRoute = `${this.getApiBasePath()}/matheus-gontijo-system-config-history/modal-data`;

        return this.httpClient.post(
            apiRoute,
            {
                localeCode: localeCode,
                defaultSalesChannelName: defaultSalesChannelName,
                modalId: modalId
            },
            {
                headers: this.getBasicHeaders(),
            }
        ).then((response) => {
            return ApiService.handleResponse(response);
        });
    }
}
