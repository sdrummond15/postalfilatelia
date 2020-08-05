/* global
 $, DataProviderHelper, SessionTimeoutError, ServerPermissionError
 */
//
var DataProvider = {};
(function () {
    'use strict';

    DataProvider.validateResponse = function validateResponse(xhr) {
        var error = DataProviderHelper.validateRequest(xhr);
        if (!error) {
            var responseText = xhr.responseText;
            if (typeof responseText === 'string') {
                if (responseText.indexOf('body id="page-login"') !== -1) {
                    error = new SessionTimeoutError();
                    error.loginUrl = _url('themler');
                } else {
                    try {
                        var obj = JSON.parse(responseText);
                        if (obj.result === 'fail') {
                            if (obj.type === 'permission') {
                                error = new ServerPermissionError(obj.error);
                            } else {
                                error = DataProviderHelper.createCmsRequestError(_url('ajax'), xhr, xhr.status, obj.error);
                            }
                        }

                        if (obj.error &&
                                (obj.message === window.themeDesigner.settings.invalidFormKeyMessage ||
                                obj.message === window.themeDesigner.settings.invalidSecretKeyMessage)) {

                            error = new SessionTimeoutError();
                            error.loginUrl = _url('themler');
                        }
                    } catch (e) {}
                }
            }
        }
        return error;
    };

    function ajaxFailHandler(url, xhr, status, callback) {
        var error = DataProvider.validateResponse(xhr);
        if (!error) {
            error = DataProviderHelper.createCmsRequestError(url, xhr, status);
        }
        callback(error);
    }

    function _url(type) {
        type = type || 'ajax';
        return window.themeDesigner[type + 'Url'] || '';
    }

    function _post(obj, action) {
        if (action) {
            obj.action = action;
        }
        obj.form_key = window.themeDesigner.formKey;
        return obj;
    }

    function _ajax(obj) {
        if (obj.data) {
            obj.data = _post(obj.data, obj.url);
        } else if (obj.post) {
            obj.post = _post(obj.post, obj.url);
        }
        obj.url = _url('ajax');
        return obj;
    }

    DataProvider.load = function load() {
        return window.themeDesigner.theme.projectData.projectData;
    };

    DataProvider.save = function save(saveData, callback) {
        var request = {
            'save': _ajax({
                'post': {
                    data: JSON.stringify(saveData)
                },
                'url': 'export'
            }),
            'clear': _ajax({
                'post': {},
                'url': 'clear'
            }),
            'errorHandler': DataProvider.validateResponse,
            'zip': true,
            'blob': true
        };
        DataProviderHelper.chunkedRequest(request, callback);
    };

    DataProvider.doExport = function doExport(data, callback) {
        var request = {
            'save':_ajax({
                'post': {
                    data: JSON.stringify(data)
                },
                'url': 'export'
            }),
            'clear': _ajax({
                'post': {},
                'url': 'clear'
            }),
            'errorHandler': DataProvider.validateResponse,
            'zip': true,
            'blob': true
        };
        DataProviderHelper.chunkedRequest(request, callback);
    };

    DataProvider.getMd5Hashes = function getMd5Hashes() {
        return window.themeDesigner.theme.hashes;
    };

    DataProvider.getAllCssJsSources = function getAllCssJsSources() {
        return window.themeDesigner.theme.cache;
    };

    DataProvider.updatePreviewTheme = function updatePreviewTheme(callback) {
        $.ajax(_ajax({
            type: 'post',
            url: 'updatePreview',
            dataType: 'json',
            data: {}
        })).done(function updatePreviewSuccess(response, status, xhr) {
            callback(DataProvider.validateResponse(xhr), response);
        }).fail(function updatePreviewFail(xhr, status) {
            ajaxFailHandler(_url('ajax'), xhr, status, callback);
        });
    };

    DataProvider.makeThemeAsActive = function makeThemeAsActive(callback, theme) {
        $.ajax(_ajax({
            type: 'post',
            url: 'publish',
            data: {
                themeName: theme
            }
        })).done(function themeActiveSuccess(response, status, xhr) {
            var error = DataProvider.validateResponse(xhr);
            if (!error) {
                window.themeDesigner.theme.active = window.themeDesigner.theme.name === theme;
            }
            callback(error);
        }).fail(function themeActiveFail(xhr, status) {
            ajaxFailHandler(_url('ajax'), xhr, status, callback);
        });
    };

    DataProvider.getTheme = function getTheme(params, callback) {
        if (params.newName === undefined) {
            params.newName = params.themeName;
            params.themeName = window.themeDesigner.theme.name;
        }

        var urlParams = Object.keys(params).map(function (param) {
            return param + '=' + params[param];
        });

        callback(null, _url('ajax') + '&action=getTheme&' + urlParams.join('&'));
    };

    DataProvider.backToAdmin = function backToAdmin() {
        window.location = _url('admin');
    };

    DataProvider.getMaxRequestSize = function getMaxRequestSize() {
        return window.themeDesigner.settings.maxRequestSize;
    };

    DataProvider.getVersion = function getVersion() {
        return '0.0.2';
    };

    function reloadInfo(action, callback) {
        $.ajax(_ajax({
            type: 'post',
            url: action,
            dataType: 'json',
            data: {}
        })).done(function reloadThemesInfoSuccess(response, status, xhr) {
            var error = DataProvider.validateResponse(xhr);
            var data = {};
            if (!error && typeof response === 'object' && response) {
                Object.keys(response.info).forEach(function (key) {
                    window.themeDesigner[key] = response.info[key];
                });
                data = response.info;
            }
            callback(error, data);
        }).fail(function reloadThemesInfoFail(xhr, status) {
            ajaxFailHandler(_url('ajax'), xhr, status, callback);
        });
    }

    DataProvider.reloadThemesInfo = function reloadThemesInfo(callback) {
        reloadInfo('reloadThemesInfo', function (error, response) {
            callback(error, JSON.stringify(response));
        });
    };

    DataProvider.getInfo = function getInfo() {
        return {
            cmsName: 'Magento',
            cmsVersion: JSON.parse(window.themeDesigner.settings.cmsVersion),
            adminPage: _url('admin'),
            startPage: _url('preview'),
            templates: window.themeDesigner.templates,
            thumbnails: [{name: 'preview.png', width: 200, height: 200}],
            isThemeActive: window.themeDesigner.theme.active,
            themeName: window.themeDesigner.theme.name,
            uploadImage: _url('upload'),
            uploadTheme: _url('uploadTheme'),
            unZip: _url('zipToFso'),
            themeArchiveExt: 'tgz',
            themes: window.themeDesigner.themes,
            pathToManifest: 'themler.manifest'
        };
    };

    DataProvider.canRename = function canRename(themeName, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        $.ajax(_ajax({
            type: 'post',
            url: 'canRename',
            dataType: 'json',
            data: {
                newName: themeName
            }
        })).done(function canRenameSuccess(response, status, xhr) {
            var error = DataProvider.validateResponse(xhr);
            if (!error) {
                callback(null, !!response.canRename);
            } else {
                callback(error);
            }
        }).fail(function canRenameFail(xhr, status) {
            ajaxFailHandler(_url('ajax'), xhr, status, callback);
        });
    };

    function renameAction(themeName, newName, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        $.ajax(_ajax({
            type: 'post',
            url: 'rename',
            dataType: 'json',
            data: {
                themeName: themeName,
                newName: newName
            }
        })).done(function renameSuccess(response, status, xhr) {
            var error = DataProvider.validateResponse(xhr);
            if (!error) {
                callback(
                    null,
                    window.themeDesigner.theme.name === themeName ?
                        window.location.toString().replace(new RegExp('theme=' + window.themeDesigner.theme.name), 'theme=' + newName) :
                        null
                );
            } else {
                callback(error);
            }
        }).fail(function renameFail(xhr, status) {
            ajaxFailHandler(_url('ajax'), xhr, status, callback);
        });
    }

    DataProvider.rename = function rename(newName, callback) {
        renameAction(window.themeDesigner.theme.name, newName, callback);
    };

    DataProvider.renameTheme = function renameTheme(themeName, newName, callBack) {
        renameAction(themeName, newName, callBack);
    };

    DataProvider.removeTheme = function removeTheme(themeName, callBack) {
        renameAction(themeName, '', callBack);
    };

    DataProvider.copyTheme = function copyTheme(themeName, newName, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        $.ajax(_ajax({
            type: 'post',
            url: 'copy',
            dataType: 'json',
            data: {
                themeName: themeName,
                newName: newName
            }
        })).done(function copySuccess(response, status, xhr) {
            var error = DataProvider.validateResponse(xhr);
            callback(error);
        }).fail(function copyFail(xhr, status) {
            ajaxFailHandler(_url('ajax'), xhr, status, callback);
        });
    };

    DataProvider.getFiles = function getFiles(mask, filter, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        $.ajax(_ajax({
            type: 'post',
            url: 'getFiles',
            dataType: 'json',
            data: {
                mask: mask,
                filter: filter
            }
        })).done(function getFilesSuccess(response, status, xhr) {
            var error = DataProvider.validateResponse(xhr);
            if (!error) {
                callback(null, response.files);
            } else {
                callback(error);
            }
        }).fail(function getFilesFail(xhr, status) {
            ajaxFailHandler(_url('ajax'), xhr, status, callback);
        });
    };

    DataProvider.setFiles = function setFiles(files, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        var request = {
            'save': _ajax({
                'post': {
                    data: JSON.stringify(files)
                },
                'url': 'setFiles'
           }),
            'clear': _ajax({
                'post': {},
                'url': 'clear'
            }),
            'errorHandler': DataProvider.validateResponse,
            'zip': true,
            'blob': true
        };

        DataProviderHelper.chunkedRequest(request, callback);
    };

    DataProvider.zip = function zip(data, callback) {
        var request = {
            'save':_ajax({
                'post': {
                    data: JSON.stringify(data)
                },
                'url': 'fsoToZip'
            }),
            'clear': _ajax({
                'post': {},
                'url': 'clear'
            }),
            'errorHandler': DataProvider.validateResponse,
            'zip': true,
            'blob': true
        };
        DataProviderHelper.chunkedRequest(request, callback);
    };

    DataProvider.escapeCustomCode = function (content) {
        return "<?php\necho <<<'CUSTOM_CODE'\n" + content + "\nCUSTOM_CODE;\n?>";
    };

}());

//