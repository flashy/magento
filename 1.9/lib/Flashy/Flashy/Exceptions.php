<?php

class Flashy_Error extends Exception {}

class Flashy_HttpError extends Flashy_Error {}

/**
 * The parameters passed to the API call are invalid or not provided when required
 */
class Flashy_ValidationError extends Flashy_Error {}

/**
 * The provided API key is not a valid Flashy API key
 */
class Flashy_Invalid_Key extends Flashy_Error {}
