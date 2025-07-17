<?php

namespace Arrtxp\Core;

interface ServiceConstants
{
    public const string NAME = 'name';
    public const string VALIDATORS = 'validators';
    public const string FILTERS = 'filters';
    public const string REQUIRED = 'required';
    public const string ALLOW_EMPTY = 'allow_empty';
    public const string OPTIONS = 'options';
    public const string DEFAULT_VALUE = 'default_value';
    public const string BREAK_CHAIN = 'break_chain_on_failure';
    public const string TYPE = 'type';
    public const string INPUT_FILTER = 'input_filter';
    public const string INPUT_STRUCTURE = 'input_structure';
    public const string INPUT_ARRAY = 'array';
    public const string INPUT_COLLECTION = 'collection';
    public const string MESSAGE_EMPTY = 'message_empty';
    public const string MESSAGE_REQUIRED = 'message_required';

    public const string ERRORS = 'errors';
    public const string PARAMS = 'params';
    public const string HTTP_CODE = 'httpCode';
    public const string EXECUTE_TIME = 'executeTime';

    public const string METHOD_POST = 'POST';
    public const string METHOD_GET = 'GET';
    public const string METHOD_PUT = 'PUT';
    public const string METHOD_PATCH = 'PATCH';
    public const string METHOD_DELETE = 'DELETE';

    public const int HTTP_CODE_200 = 200;
    public const int HTTP_CODE_204 = 204;
    public const int HTTP_CODE_301 = 301;
    public const int HTTP_CODE_302 = 302;
    public const int HTTP_CODE_400 = 400;
    public const int HTTP_CODE_401 = 401;
    public const int HTTP_CODE_402 = 402;
    public const int HTTP_CODE_403 = 403;
    public const int HTTP_CODE_404 = 404;
    public const int HTTP_CODE_410 = 410;
    public const int HTTP_CODE_500 = 500;
    public const int HTTP_CODE_503 = 503;
}