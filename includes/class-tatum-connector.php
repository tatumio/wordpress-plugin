<?php

class Tatum_Connector {
	const TATUM_URL = 'https://api-eu1.tatum.io';

	private string $api_key;

	/**
	 * Tatum_Connector constructor.
	 *
	 * @param $api_key
	 */
	public function __construct( $api_key ) {
		$this->api_key = $api_key;
	}

	private static function get( $url, $api_key ) {
		$ch = curl_init( self::TATUM_URL . $url );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, self::headers( $api_key ) );

		$server_output = curl_exec( $ch );
		$code          = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		if ( substr( $code, 0, strlen( '2' ) ) !== '2' ) {
			throw new Exception( $server_output );
		}

		if ( curl_errno( $ch ) ) {
			$error_msg = curl_error( $ch );
			throw new Exception( $error_msg );
		}

		return json_decode( $server_output, true );
	}

	private static function post( $url, $body, $api_key ) {
		$ch = curl_init( self::TATUM_URL . $url );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $body ) );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, self::headers( $api_key ) );

		$server_output = curl_exec( $ch );
		$code          = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		if ( substr( $code, 0, strlen( '2' ) ) !== '2' ) {
			throw new Exception( $server_output );
		}

		if ( curl_errno( $ch ) ) {
			$error_msg = curl_error( $ch );
			throw new Exception( $error_msg );
		}

		return json_decode( $server_output, true );
	}

	private static function headers( $api_key ): array {
		return [
			'Accept: application/json',
			'Content-Type: application/json',
			'x-api-key:' . $api_key,
		];
	}

	public static function get_api_version( $api_key ) {
		return self::get( '/v3/tatum/version', $api_key );
	}

	public static function generate_ethereum_wallet( $api_key ) {
		return self::get( '/v3/ethereum/wallet', $api_key );
	}

	public static function generate_celo_wallet( $api_key ) {
		return self::get( '/v3/celo/wallet', $api_key );
	}

	public static function generate_ethereum_account( $xpub, $index, $api_key ) {
		return self::get( '/v3/ethereum/address/' . $xpub . '/' . $index, $api_key );
	}

	public static function generate_celo_account( $xpub, $index, $api_key ) {
		return self::get( '/v3/celo/address/' . $xpub . '/' . $index, $api_key );
	}

	public static function generate_ethereum_private_key( $body, $api_key ) {
		return self::post( '/v3/ethereum/wallet/priv/', $body, $api_key );
	}

	public static function generate_celo_private_key( $body, $api_key ) {
		return self::post( '/v3/celo/wallet/priv/', $body, $api_key );
	}

	public static function deploy_nft_smart_contract( $body, $api_key ) {
		return self::post( '/v3/nft/deploy', $body, $api_key );
	}

	public static function mint_nft( $body, $api_key ) {
		return self::post( '/v3/nft/mint', $body, $api_key );
	}

	public static function get_ethereum_balance( $address, $api_key ) {
		return self::get( '/v3/ethereum/account/balance/' . $address, $api_key );
	}

	public function get_balance( $address, $api_key = null ) {
		return self::get( '/v3/ethereum/account/balance/' . $address, $api_key ?? $this->api_key );
	}

	public static function get_celo_balance( $address, $api_key ) {
		return self::get( '/v3/celo/account/balance/' . $address, $api_key );
	}

	public static function get_ethereum_transaction( $hash, $api_key ) {
		return self::get( '/v3/ethereum/transaction/' . $hash, $api_key );
	}

	public static function get_celo_transaction( $hash, $api_key ) {
		return self::get( '/v3/celo/transaction/' . $hash, $api_key );
	}

	public static function transfer_nft_token( $body, $api_key ) {
		return self::post( '/v3/nft/transaction', $body, $api_key );
	}
}