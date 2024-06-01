/**
 * claims Home version 1 controllers
 */
angular.module('starter')
    .controller('ClaimsController', function (Dialog, Loader, Claims, Location, $filter, Customer, GoogleMaps, $rootScope, SB, $scope, $state, $stateParams, $translate, $interval, $timeout, $ionicModal) {
        $scope.value_id = Claims.value_id = $stateParams.value_id;
        $scope.is_logged_in = Customer.isLoggedIn();
        $scope.customer = Customer.customer;
        $scope.is_loading = false;
        $scope.claimsList = {};

        $rootScope.$on(SB.EVENTS.AUTH.loginSuccess, function () {
            if ($scope.value_id) {
                $scope.loadContent();
            }
            $scope.is_logged_in = Customer.isLoggedIn();
            $scope.customer = Customer.customer;
        });

        $rootScope.$on(SB.EVENTS.AUTH.logoutSuccess, function () {
            if ($scope.value_id) {
                $scope.loadContent();
            }
            $scope.loadContent();
            $scope.is_logged_in = Customer.isLoggedIn();
            $scope.customer = Customer.customer;
        });

        /**
         *login
         */
        $scope.login = function () {
            Customer.loginModal($scope);
        }

        /**
         *Load content
         */
        $scope.loadContent = function () {
            if (!Customer.isLoggedIn()) {
                return false;
            }
            $scope.is_loading = true;

            Claims.findAll().success(function (data) {
                $scope.page_title = data.page_title;
                $scope.claimsList = data.claims;

            }).error(function (error) {
                $scope.is_loading = false;
            }).finally(function () {
                $scope.is_loading = false;
            });
        }

        $scope.loadContent();

    }).controller('ClaimsViewController', function (Dialog, Loader, Application, $cordovaCamera, $ionicActionSheet, $ionicPopup, Claims, Location, $filter, Customer, GoogleMaps, $rootScope, SB, $scope, $state, $stateParams, $translate, $interval, $timeout, $ionicModal) {
    $scope.value_id = Claims.value_id = $stateParams.value_id;
    $scope.claim_id = $stateParams.id;
    $scope.is_logged_in = Customer.isLoggedIn();
    $scope.customer = Customer.customer;
    $scope.is_loading = false;
    $scope.claim_info = {};
    $scope.categories = {};
    $scope.claim_post = {
        images: [],
        claim_id: $scope.claim_id
    };

    $scope.loadContent = function () {
        $scope.is_loading = true;

        Claims.claimDetailsById($scope.claim_id).success(function (data) {
            $scope.is_loading = false;
            $scope.claim_info = data.claim_info;
            $scope.categories = data.categories;
            $scope.claimsItems = data.items;
            $scope.allow_edit = data.allow_edit;
        }).error(function (error) {
            Loader.hide();
            Dialog.alert($translate.instant("Error", "claims"), error.message, "OK", -1, "claims");
        });
    }

    /**
     * add Item
     */
    $scope.addItem = function () {
        $scope.claim_post = {
            images: [],
            claim_id: $scope.claim_id
        };
        $ionicModal.fromTemplateUrl('features/claims/assets/templates/l1/modal/add_item.html', {
            scope: $scope,
            animation: 'slide-in-up'
        }).then(function (modal) {
            $scope.addItemModal = modal;
            $scope.addItemModal.show();
        });
    };

    /**
     * Close add Item
     */
    $scope.closeAddItemModal = function () {
        $scope.addItemModal.remove();
    }

    //Add photos
    $scope.addPicture = function () {
        var gotImage = function (image_url) {
            $scope.imageToSend = image_url;
            $ionicPopup.show({
                template: '<center><img ng-src="{{imageToSend}}" style="max-width: 100%; max-height: 100%;"></center>',
                cssClass: 'classifieds no-title',
                scope: $scope,
                buttons: [{
                    text: $translate.instant("Cancel", "claims"),
                    type: 'button-default',
                    onTap: function (e) {
                        return false;
                    }
                }, {
                    text: $translate.instant("OK", "claims"),
                    type: 'button-positive',
                    onTap: function (e) {
                        return true;
                    }
                }]
            }).then(function (result) {
                if (result) {
                    if ($scope.claim_post.images.includes(image_url) === false) {
                        $scope.claim_post.images.push(image_url);
                    } else {
                        Dialog.alert("Error", "This image is already added!", "OK", -1, "claims");
                    }
                }
            }).finally(function () {
                $scope.imageToSend = null;
            });
        };

        var gotError = function (err) {
            // An error occured. Show a message to the user
        };

        if (Application.is_webview) {
            var input = angular.element("<input type='file' accept='image/*'>");
            var selectedFile = function (evt) {
                var file = evt.currentTarget.files[0];
                var reader = new FileReader();
                reader.onload = function (evt) {
                    gotImage(evt.target.result);
                    input.off("change", selectedFile);
                };
                reader.onerror = gotError;
                reader.readAsDataURL(file);
            };
            input.on("change", selectedFile);
            input[0].click();
        } else {
            var source_type = Camera.PictureSourceType.CAMERA;

            // Show the action sheet
            var hideSheet = $ionicActionSheet.show({
                buttons: [{
                    text: $translate.instant("Take a picture", "claims")
                },
                    {
                        text: $translate.instant("Import from Library", "claims")
                    }
                ],
                cancelText: $translate.instant("Cancel", "claims"),
                cancel: function () {
                    hideSheet();
                },
                buttonClicked: function (index) {
                    if (index == 0) {
                        source_type = Camera.PictureSourceType.CAMERA;
                    }
                    if (index == 1) {
                        source_type = Camera.PictureSourceType.PHOTOLIBRARY;
                    }

                    var options = {
                        quality: 90,
                        destinationType: Camera.DestinationType.DATA_URL,
                        sourceType: source_type,
                        encodingType: Camera.EncodingType.JPEG,
                        targetWidth: 800,
                        targetHeight: 800,
                        correctOrientation: true,
                        popoverOptions: CameraPopoverOptions,
                        saveToPhotoAlbum: false
                    };

                    $cordovaCamera.getPicture(options).then(function (imageData) {
                        gotImage("data:image/jpeg;base64," + imageData);
                    }, gotError);

                    return true;
                }
            });
        }
    };

    // for remove images from array
    $scope.removeImage = function (item) {
        var index = $scope.claim_post.images.indexOf(item);
        $scope.claim_post.images.splice(index, 1);
    }

    $scope.claimItemSave = function (type) {
        Loader.show();
        Claims.saveClaimItem($scope.claim_post).success(function (data) {
            if (type == 1) {
                $scope.closeAddItemModal();
                $scope.loadContent();
            } else {
                $scope.claim_post = {
                    images: [],
                    claim_id: $scope.claim_id
                };
                $scope.claimsItems = data.items;
            }
            Loader.hide();

        }).error(function (error) {
            Loader.hide();
            Dialog.alert($translate.instant("Error", "claims"), error.message, "OK", -1, "claims");
        });
    }

    $scope.submitClaim = function () {
        var buttons = [$translate.instant("Yes", "claims"), $translate.instant("No", "claims")];
        Dialog
            .confirm('Confirmation', $translate.instant("Are you sure want to submit?", "claims"), buttons, 'text-center')
            .then(function (result) {
                if (result) {

                    Loader.show();
                    Claims.submitClaim($scope.claim_id).success(function (data) {

                        Dialog.alert($translate.instant("Success", "claims"), $translate.instant("Your claim is successfully submitted", "claims"), "OK", -1, "claims");
                        $state.go("claims-home", {value_id: $scope.value_id}, {reload: true});

                        Loader.hide();
                    }).error(function (error) {
                        Loader.hide();
                        Dialog.alert($translate.instant("Error", "claims"), error.message, "OK", -1, "claims");
                    });
                }
            });
    }


    $scope.deleteItem = function (item_id) {

        var buttons = [$translate.instant("Yes", "claims"), $translate.instant("No", "claims")];
        Dialog
            .confirm('Confirmation', $translate.instant("Are you sure want to delete this?", "claims"), buttons, 'text-center')
            .then(function (result) {
                if (result) {
                    Loader.show();
                    Claims.deleteItem(item_id).success(function (data) {
                        $scope.loadContent();
                        Loader.hide();
                    }).error(function (error) {
                        Loader.hide();
                        Dialog.alert($translate.instant("Error", "claims"), error.message, "OK", -1, "claims");
                    });
                }
            });
    }


    $scope.loadContent(); // Initial Load


}).controller('ClaimsAddController', function (Dialog, Loader, Claims, Location, $ionicHistory, $filter, Customer, GoogleMaps, $rootScope, SB, $scope, $state, $stateParams, $translate, $interval, $timeout, $ionicModal) {
    $scope.value_id = Claims.value_id = $stateParams.value_id;
    $scope.is_logged_in = Customer.isLoggedIn();
    $scope.customer = Customer.customer;
    $scope.is_loading = false;
    $scope.claim_post = {
        title: '',
        location: '',
        start_date: ''
    };

    $scope.claimSave = function () {
        Loader.show();

        Claims.claimSave($scope.claim_post).success(function (data) {
            $ionicHistory.nextViewOptions({
                historyRoot: true,
                disableAnimate: false
            });

            $state
                .go('home')
                .then(function () {
                    $state
                        .go("claims-home", {value_id: $scope.value_id}, {reload: true})
                        .then(function () {
                            Loader.hide();
                            $state.go("claims-view", {value_id: $scope.value_id, id: data.claim_id}, {reload: true});
                        });
                    ;
                });

        }).error(function (error) {
            Loader.hide();
            Dialog.alert($translate.instant("Error", "claims"), error.message, "OK", -1, "claims");
        });

    }


});