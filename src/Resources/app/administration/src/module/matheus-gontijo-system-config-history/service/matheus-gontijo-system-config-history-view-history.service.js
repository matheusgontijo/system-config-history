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
        
        
        
        
        
        
        // return this.httpClient.post(
        //     route,
        //     {
        //         customerIds: Array.isArray(customerId) ? customerId : [customerId],
        //         ...additionalRequest,
        //     },
        //     {
        //         params: additionalParams,
        //         headers: this.getBasicHeaders(additionalHeaders),
        //     },
        // ).then((response) => {
        //     return ApiService.handleResponse(response);
        // });
        //
        //
        // return this.httpClient.post(
        //     apiRoute,
        //     formData,
        //     {
        //         headers: this.getBasicHeaders(),
        //     },
        // ).then((response) => {
        //     return ApiService.handleResponse(response);
        // });
    }


    // /**
    //  * @returns {Promise<Object>}
    //  */
    // async getPlaceholders() {
    //     const apiRoute = `${this.getApiBasePath()}/config/placeholders`;
    //
    //     return this.httpClient.get(apiRoute, {
    //         headers: this.getBasicHeaders(),
    //     }).then((response) => {
    //         return ApiService.handleResponse(response);
    //     });
    // }
    //
    // /**
    //  * @returns {Promise<Object>}
    //  */
    // async getCustomFields() {
    //     const apiRoute = `${this.getApiBasePath()}/config/custom-fields`;
    //
    //     return this.httpClient.get(apiRoute, {
    //         headers: this.getBasicHeaders(),
    //     }).then((response) => {
    //         return ApiService.handleResponse(response);
    //     });
    // }
    //
    // /**
    //  * @returns {Promise<Object>}
    //  */
    // testCredentials(appKey, appSecret, baseUri) {
    //     const apiRoute = `${this.getApiBasePath()}/config/test-credentials`;
    //
    //     const params = {
    //         appKey   : appKey,
    //         appSecret: appSecret,
    //         baseUri  : baseUri,
    //     };
    //
    //     return this.httpClient.post(apiRoute, params, {
    //         headers: this.getBasicHeaders(),
    //     }).then((response) => {
    //         return ApiService.handleResponse(response);
    //     });
    // }
    //
    // /**
    //  * @returns {Promise<Object>}
    //  */
    // generateAccessToken(salesChannelId) {
    //     const apiRoute = `${this.getApiBasePath()}/config/generate-access-token`;
    //
    //     const params = {
    //         salesChannelId : salesChannelId,
    //     };
    //
    //     return this.httpClient.post(apiRoute, params, {
    //         headers: this.getBasicHeaders(),
    //     }).then((response) => {
    //         return ApiService.handleResponse(response);
    //     });
    // }
}
