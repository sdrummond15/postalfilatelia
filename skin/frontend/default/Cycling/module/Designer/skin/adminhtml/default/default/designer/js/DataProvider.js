/* global
 $, DataProviderHelper, SessionTimeoutError, LayoutContainer, ErrorUtility, ServerPermissionError
*/
//
var DataProvider = {};
(function () {
    'use strict';

    function validateResponse(xhr) {
        var error = DataProviderHelper.validateRequest(xhr);
        if (!error) {
            var responseText = xhr.responseText;
            if (typeof responseText === 'string') {
                if (responseText.indexOf('refresh the page') !== -1 || responseText.indexOf('body id="page-login"') !== -1) {
                    error = new SessionTimeoutError();
                    error.loginUrl = window.themeDesigner.url.designer;
                } else {
                    try {
                        var obj = JSON.parse(responseText);
                        if (obj.result === 'fail' && obj.type === 'permission') {
                            error = new ServerPermissionError(obj.error);
                        }
                    } catch (e) {
                    }
                }
            }
        }
        return error;
    }

    function ajaxFailHandler(url, xhr, status, callback) {
        var error = validateResponse(xhr);
        if (!error) {
            error = ErrorUtility.createRequestError(url, xhr, status, 'Request fail');
        }
        callback(error);
    }

    DataProvider.load = function load() {
        return window.themeDesigner.theme.projectData.projectData;
    };

    DataProvider.save = function save(saveData, callback) {
        var request = {
            'save': {
                'post': {
                    data: JSON.stringify(saveData),
                    form_key: window.themeDesigner.ajaxUrl.key
                },
                'url': window.themeDesigner.ajaxUrl.cmsExport
            },
            'clear': {
                'post': {
                    form_key: window.themeDesigner.ajaxUrl.key
                },
                'url': window.themeDesigner.ajaxUrl.clear
            },
            'errorHandler': validateResponse,
            'encode': true
        };
        DataProviderHelper.chunkedRequest(request, callback);
    };

    DataProvider.doExport = function doExport(data, callback) {
        var request = {
            'save': {
                'post': {
                    data: JSON.stringify(data),
                    form_key: window.themeDesigner.ajaxUrl.key
                },
                'url': window.themeDesigner.ajaxUrl.cmsExport
            },
            'clear': {
                'post': {
                    form_key: window.themeDesigner.ajaxUrl.key
                },
                'url': window.themeDesigner.ajaxUrl.clear
            },
            'errorHandler': validateResponse,
            'encode': true
        };
        DataProviderHelper.chunkedRequest(request, callback);
    };

    DataProvider.convertHtmlToCms = function convertHtmlToCms(fso, control) {
        var controlName = control.constructorName.toLowerCase();
        var callParams = [
            "'converted/" + controlName + "'",
            control.id
        ];
        if (fso.exists('page.html')) {
            var controlView = $.trim(fso.read('page.html'));
            if (controlView && /^<\?php[\s\S]+\?>$/.test($.trim(controlView)) === false) {
                var controlCall;
                if (control instanceof LayoutContainer) {
                    controlCall = "<?php include Mage::getSingleton('core/design_package')->getBaseDir(array('_type' => 'template', '_default' => false)) . '/designer/converted/" + controlName + '_' + control.id + ".phtml' ?>";
                } else {
                    controlCall = "<?php echo Mage::helper('designer')->createTemplate(" + callParams.join(', ') + ")->setContext($this)->toHtml() ?>";
                }
                fso.write('page.html', controlCall);
                fso.write('includes/converted/' + controlName + '_' + control.id + '.phtml', controlView);
            }
        }
        return fso;
    };

    DataProvider.getMd5Hashes = function getMd5Hashes() {
        return window.themeDesigner.theme.hashes;
    };

    DataProvider.getAllCssJsSources = function getAllCssJsSources() {
        return window.themeDesigner.theme.cache;
    };

    DataProvider.updatePreviewTheme = function updatePreviewTheme(callback) {
        $.ajax({
            type: 'get',
            url: window.themeDesigner.ajaxUrl.updatePreview
        })
        .done(function updatePreviewSuccess(response, status, xhr) {
            var error = validateResponse(xhr);
            callback(error);
        })
        .fail(function updatePreviewFail(xhr, status) {
            ajaxFailHandler(window.themeDesigner.ajaxUrl.updatePreview, xhr, status, callback);
        });
    };

    DataProvider.makeThemeAsActive = function makeThemeAsActive(callback) {
        $.ajax({
            type: 'get',
            url: window.themeDesigner.ajaxUrl.publish,
            data: {
                form_key: window.themeDesigner.ajaxUrl.key
            }
        }).done(function themeActiveSuccess(response, status, xhr) {
            var error = validateResponse(xhr);
            if (!error) {
                window.themeDesigner.theme.active = true;
            }
            callback(error);
        }).fail(function themeActiveFail(xhr, status) {
            ajaxFailHandler(window.themeDesigner.ajaxUrl.publish, xhr, status, callback);
        });
    };

    DataProvider.getTheme = function getTheme(themeName, callback) {
        $.ajax({
            type: 'get',
            url: window.themeDesigner.ajaxUrl.getTheme,
            dataType: 'text',
            data: {
                form_key: window.themeDesigner.ajaxUrl.key,
                themeName: themeName
            }
        }).done(function getThemeRequestSuccess(response, status, xhr) {
            var error = validateResponse(xhr);
            if (!error) {
                callback(null, response);
            } else {
                callback(error);
            }
        }).fail(function getThemeRequestFail(xhr, status) {
            ajaxFailHandler(window.themeDesigner.ajaxUrl.getTheme, xhr, status, callback);
        });
    };

    DataProvider.backToAdmin = function backToAdmin() {
        window.location = window.themeDesigner.url.admin;
    };

    DataProvider.getInfo = function getInfo() {
        return {
            cmsName: 'Magento',
            cmsVersion: JSON.parse(window.themeDesigner.settings.cmsVersion),
            adminPage: window.themeDesigner.url.admin,
            startPage: window.themeDesigner.url.preview,
            templates: window.themeDesigner.templates,
            thumbnails: [{name: 'preview.png', width: 100, height: 100}],
            isThemeActive: window.themeDesigner.theme.active,
            themeName: window.themeDesigner.theme.name,
            uploadImage: window.themeDesigner.ajaxUrl.uploadImage,
            themeArchiveExt: 'tgz'
        };
    };

    DataProvider.canRename = function canRename(themeName, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        $.ajax({
            type: 'post',
            url: window.themeDesigner.ajaxUrl.canRename,
            dataType: 'json',
            data: {
                form_key: window.themeDesigner.ajaxUrl.key,
                newName: themeName
            }
        }).done(function canRenameSuccess(response, status, xhr) {
            var error = validateResponse(xhr);
            if (!error) {
                callback(null, !!response.canRename);
            } else {
                callback(error);
            }
        }).fail(function canRenameFail(xhr, status) {
            ajaxFailHandler(window.themeDesigner.ajaxUrl.canRename, xhr, status, callback);
        });
    };

    DataProvider.rename = function rename(themeName, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        $.ajax({
            type: 'post',
            url: window.themeDesigner.ajaxUrl.rename,
            dataType: 'json',
            data: {
                form_key: window.themeDesigner.ajaxUrl.key,
                newName: themeName
            }
        }).done(function renameSuccess(response, status, xhr) {
            var error = validateResponse(xhr);
            if (!error) {
                callback(null, window.location.toString().replace(new RegExp('theme=' + window.themeDesigner.theme.name), 'theme=' + themeName));
            } else {
                callback(error);
            }
        }).fail(function renameFail(xhr, status) {
            ajaxFailHandler(window.themeDesigner.ajaxUrl.rename, xhr, status, callback);
        });
    };

    DataProvider.getFiles = function getFiles(mask, filter, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        $.ajax({
            type: 'post',
            url: window.themeDesigner.ajaxUrl.getFiles,
            dataType: 'json',
            data: {
                form_key: window.themeDesigner.ajaxUrl.key,
                mask: mask,
                filter: filter
            }
        }).done(function getFilesSuccess(response, status, xhr) {
            var error = validateResponse(xhr);
            if (!error) {
                callback(null, response.files);
            } else {
                callback(error);
            }
        }).fail(function getFilesFail(xhr, status) {
            ajaxFailHandler(window.themeDesigner.ajaxUrl.getFiles, xhr, status, callback);
        });
    };

    DataProvider.setFiles = function setFiles(files, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        var request = {
            'save': {
                'post': {
                    form_key: window.themeDesigner.ajaxUrl.key,
                    data: JSON.stringify(files)
                },
                'url': window.themeDesigner.ajaxUrl.setFiles
            },
            'clear': {
                'post': {
                    form_key: window.themeDesigner.ajaxUrl.key
                },
                'url': window.themeDesigner.ajaxUrl.clear
            },
            'errorHandler': validateResponse,
            'encode': true
        };

        DataProviderHelper.chunkedRequest(request, callback);
    };

}());

//