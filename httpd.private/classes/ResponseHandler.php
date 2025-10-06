<?php
class ResponseHandler {
    private $response = [];

    public function addData($key, $value) {
        $this->response[$key] = $value;
    }

    public function sendResponse($success, $message) {
        $this->response['success'] = $success;
        $this->response['message'] = $message;
        header('Content-Type: application/json');
        return json_encode($this->response);
    }
}
?>