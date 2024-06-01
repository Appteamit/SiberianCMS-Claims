/**
 * Claims factory
 */
angular
    .module('starter')
    .factory('Claims', function ($state, $pwaRequest) {
        var factory = {};
        factory.value_id = null;

        factory.setValueId = function (valueId) {
            factory.value_id = valueId;
            return factory;
        };

        factory.getValueId = function () {
            return factory.value_id;
        };

        factory.findAll = function () {

            return $pwaRequest.post('/claims/mobile_view/findall', {
                urlParams: {
                    value_id: factory.value_id
                },
                cache: false
            });
        };

        factory.submitClaim = function (claim_id) {

            return $pwaRequest.post('/claims/mobile_view/submit-claim', {
                urlParams: {
                    value_id: factory.value_id,
                    claim_id: claim_id
                },
                cache: false
            });
        };

        factory.deleteItem = function (item_id) {

            return $pwaRequest.post('/claims/mobile_view/delete-item', {
                urlParams: {
                    value_id: factory.value_id,
                    item_id: item_id
                },
                cache: false
            });
        };


        factory.claimDetailsById = function (claim_id) {

            return $pwaRequest.post('/claims/mobile_view/claim-details-by-id', {
                urlParams: {
                    value_id: factory.value_id,
                    claim_id: claim_id
                },
                cache: false
            });
        };


        factory.claimSave = function (params) {

            return $pwaRequest.post('/claims/mobile_view/claim-save', {
                urlParams: {
                    value_id: factory.value_id
                },
                data: params,
                cache: false
            });
        };

        factory.saveClaimItem = function (data) {

            return $pwaRequest.post('/claims/mobile_view/save-claim-item', {
                data: {
                    item: data,
                    value_id: factory.value_id
                },
                refresh: true
            });
        };

        return factory;
    });