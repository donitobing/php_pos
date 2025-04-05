<?php
class Core {
    protected $currentController = DEFAULT_CONTROLLER;
    protected $currentAction = DEFAULT_ACTION;
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();
        
        if(isset($url[0])) {
            $controllerName = ucwords($url[0]);
            $controllerFile = 'app/controllers/' . $controllerName . '.php';
            if(file_exists($controllerFile)) {
                $this->currentController = $controllerName;
                unset($url[0]);
            }
        } else {
            $controllerName = ucwords($this->currentController);
            $this->currentController = $controllerName;
        }

        // Load the controller file
        $controllerFile = 'app/controllers/' . $this->currentController . '.php';
        if (!file_exists($controllerFile)) {
            die('Controller file not found: ' . $controllerFile);
        }
        require_once $controllerFile;

        // Create controller instance
        if (!class_exists($this->currentController)) {
            die('Controller class not found: ' . $this->currentController);
        }
        $this->currentController = new $this->currentController;

        // Determine the action
        if(isset($url[1])) {
            if(method_exists($this->currentController, $url[1])) {
                $this->currentAction = $url[1];
                unset($url[1]);
            }
        }

        // Get params
        $this->params = $url ? array_values($url) : [];

        // Call the method
        if (!method_exists($this->currentController, $this->currentAction)) {
            die('Method not found: ' . get_class($this->currentController) . '::' . $this->currentAction);
        }
        call_user_func_array([$this->currentController, $this->currentAction], $this->params);
    }

    protected function getUrl() {
        if(isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
    }
}
