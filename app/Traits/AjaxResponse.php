<?php

namespace App\Traits;

trait AjaxResponse
{
    protected function jsonResponse($data = [], $status = 200) {
        $response = ['status' => $status >= 200 && $status < 300 ? 'success' : 'error'];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        if (session()->has('success')) {
            $response['messages']['success'] = session()->get('success');
        }

        if (session()->has('error')) {
            $response['messages']['error'] = session()->get('error');
        }

        if (session()->has('warning')) {
            $response['messages']['warning'] = session()->get('warning');
        }

        if (session()->has('info')) {
            $response['messages']['info'] = session()->get('info');
        }

        return response()->json($response, $status);
    }

    public function success($data = [], $message = null) {
        if ($message) {
            session()->flash('success', $message);
        }
        return $this->jsonResponse($data);
    }

    public function error($message = null, $data = [], $status = 400) {
        if ($message) {
            session()->flash('error', $message);
        }
        return $this->jsonResponse($data, $status);
    }
}
