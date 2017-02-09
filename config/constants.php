<?php

class constants {
    // Success
    const API_RESPONSE_CODE_OK = 200;
    const API_RESPONSE_CODE_CREATED = 201;
    const API_RESPONSE_CODE_NO_CONTENT = 204;

    // Redirection
    const API_RESPONSE_CODE_NOT_MODIFIED = 304;

    // Client Error
    const API_RESPONSE_CODE_BAD_REQUEST = 400;
    const API_RESPONSE_CODE_ANAUTHORIZED = 401;
    const API_RESPONSE_CODE_FORBIDDEN = 403;
    const API_RESPONSE_CODE_NOT_FOUND = 404;
    const API_RESPONSE_CODE_CONFLICT = 409;
    const API_RESPONSE_CODE_UNPROCESSABLE = 422;

    // Server Error
    const API_RESPONSE_CODE_INTER_SERVER_ERROR = 500;

    // Number member in group
    const MAX_MEMBER_GROUP = 5;

    // Max length string token
    const MAX_LENGTH_TOKEN = 60;
}
