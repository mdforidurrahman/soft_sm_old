<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SMSService
{
	protected $apiKey;
	protected $senderId;
	protected $baseUrl = 'http://bulksmsbd.net/api/';

	public function __construct() {
		$this->apiKey = config('sms.api_key');
		$this->senderId = config('sms.sender_id');
	}

	/**
	 * Send a single SMS
	 */
	public function sendSingleSMS(string $number, string $message): array {
		$url = $this->baseUrl . 'smsapi';

		$data = [
			'api_key' => $this->apiKey,
			'senderid' => $this->senderId,
			'number' => $number,
			'message' => $message,
			'type' => 'text'
		];

		return $this->sendRequest($url, $data);
	}

	/**
	 * Send multiple SMS messages
	 */
	public function sendMultipleSMS(array $messages): array {
		$url = $this->baseUrl . 'smsapimany';

		$data = [
			'api_key' => $this->apiKey,
			'senderid' => $this->senderId,
			'messages' => json_encode($messages)
		];

		return $this->sendRequest($url, $data);
	}

	/**
	 * Send sale confirmation SMS
	 */
//	public function sendSaleConfirmation(string $customerNumber, string $customerName, array $saleDetails): array {
//		$message = "Dear $customerName, thank you for your purchase!\n";
//		$message .= "Order #: {$saleDetails['order_id']}\n";
//		$message .= "Amount: {$saleDetails['amount']} BDT\n";
//		$message .= "Paid Amount: {$saleDetails['amount']} BDT\n";
//		$message .= "Discount Amount: {$saleDetails['discount']}\n";
//		$message .= "Due Amount: {$saleDetails['paymentDue']}\n";
//
//		if (isset($saleDetails['delivery_address'])) {
//			$message .= "Delivery: {$saleDetails['delivery_address']}\n";
//		}
//
//		$message .= 'Contact us for any queries.';
//
//		return $this->sendSingleSMS($customerNumber, $message);
//	}


	public function sendSaleConfirmation(string $customerNumber, string $customerName, array $saleDetails): array {
		// Original message with corrected paid amount
		// $message = "Dear $customerName, thank you for your purchase!\n";
		// $message .= "Order #: {$saleDetails['order_id']}\n";
		// $message .= "Amount: {$saleDetails['amount']} BDT\n";
		// $message .= "Paid Amount: {$saleDetails['paid_amount']} BDT\n"; // Fixed this line
		// $message .= "Discount Amount: {$saleDetails['discount']}\n";
		// $message .= "Due Amount: {$saleDetails['paymentDue']}\n";

		$message = "প্রিয় {$customerName} ভাই, ";

		// Get product info if available
		$productInfo = isset($saleDetails['product_name']) ?
			"{$saleDetails['product_name']} কোর জন্য ধন্বাদ। " :
			'ব্যটারি কেনাকটার জন্য ধনযবাদ। ';
		$message .= $productInfo;
		$message .= "ইডি নম্বর-{$saleDetails['order_id']} ";
		$message .= "মোট বাকি-{$saleDetails['amount']}, ";
		$message .= "জমা-{$saleDetails['paid_amount']}, ";
		$message .= "অবশিষ্ট বাকি-{$saleDetails['paymentDue']}।";

		// Add delivery address if available
		if (isset($saleDetails['delivery_address'])) {
			$message .= "\nডেলিভারি ঠিকান: {$saleDetails['delivery_address']}";
		}

		// Add contact information
		$message .= "\nযকোনো প্রয়জনে যোগাযোগ করুন: ০১৩০০৪৫৯২";

		return $this->sendSingleSMS($customerNumber, $message);
	}

	/**
	 * Common request handler
	 */
	protected function sendRequest(string $url, array $data): array {
		try {
			$client = new \GuzzleHttp\Client();
			$response = $client->post($url, ['form_params' => $data]);

			$responseData = json_decode($response->getBody(), true);

			return [
				'success' => true,
				'data' => $responseData,
				'status' => $response->getStatusCode()
			];
		} catch (\Exception $e) {
			Log::error('SMS sending failed: ' . $e->getMessage());

			return [
				'success' => false,
				'error' => $e->getMessage(),
				'status' => $e->getCode() ?: 500
			];
		}
	}

	/**
	 * Get error message from code
	 */
	public function getErrorMessage(int $code): string {
		$errors = [
			202 => 'SMS Submitted Successfully',
			1001 => 'Invalid Number',
		];

		return $errors[$code] ?? 'Unknown error';
	}

	public function sendPaymentConfirmation(string $customerNumber, string $customerName, array $paymentDetails): array {
		// Bengali payment confirmation message
		$message = "প্রিয় {$customerName} ! ";
		$message .= 'এস.এম.ানলাইট গ্রপ থেকে পণ্য ্রয় করার জ্য আপনাকে সবাগতম।';
		$message .= "আইডি নম্ব-{$paymentDetails['order_id']} ";
		$message .= "মোট বাকি-{$paymentDetails['amount']}, ";
		$message .= "জম-{$paymentDetails['paid_amount']}, ";

		// Add discount info if any
		if ($paymentDetails['discount'] > 0) {
			$message .= "ছাড়-{$paymentDetails['discount']}, ";
		}

		$message .= "অবশিষ্ট বাি-{$paymentDetails['paymentDue']}।";
		$message .= "\n প্রয়োজনে যোাযোগ করুন: ০১৩০০৯৪৫৯২";

		return $this->sendSingleSMS($customerNumber, $message);

	}
}