<?php

class Tatum_Connector {
	const TATUM_URL = 'https://api-eu1.tatum.io';

	const BLOCKCHAIN_URL_MAPPING = [ 'ETH' => 'ethereum', 'CELO' => 'celo', 'BSC' => 'bsc' ];

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

	public static function generate_wallet( $chain, $api_key ) {
		return self::get( '/v3/' . self::BLOCKCHAIN_URL_MAPPING[ $chain ] . '/wallet', $api_key );
	}

	public static function generate_account( $chain, $xpub, $index, $api_key ) {
		return self::get( '/v3/' . self::BLOCKCHAIN_URL_MAPPING[ $chain ] . '/address/' . $xpub . '/' . $index, $api_key );
	}

	public static function generate_private_key( $chain, $body, $api_key ) {
		return self::post( '/v3/' . self::BLOCKCHAIN_URL_MAPPING[ $chain ] . '/wallet/priv/', $body, $api_key );
	}

	public static function deploy_nft_smart_contract( $body, $api_key ) {
		return self::post( '/v3/nft/deploy', $body, $api_key );
	}

	public static function mint_nft( $body, $api_key ) {
		return self::post( '/v3/nft/mint', $body, $api_key );
	}

	public static function get_balance( $chain, $address, $api_key ) {
		return self::get( '/v3/' . self::BLOCKCHAIN_URL_MAPPING[ $chain ] . '/account/balance/' . $address, $api_key );
	}

	public static function get_transaction( $chain, $hash, $api_key ) {
		return self::get( '/v3/' . self::BLOCKCHAIN_URL_MAPPING[ $chain ] . '/transaction/' . $hash, $api_key );
	}

	public static function transfer_nft_token( $body, $api_key ) {
		return self::post( '/v3/nft/transaction', $body, $api_key );
	}
}