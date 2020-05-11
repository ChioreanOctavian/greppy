<?php


namespace greppy\Routing;

use greppy\Contracts\RouterInterface;
use greppy\Http\Request;

class Router implements RouterInterface
{
    const CONFIG_ROUTER = "router";
    const CONFIG_ROUTES = "routes";
    const CONFIG_ROUTES_KEY_PATH = "path";
    const CONFIG_ROUTES_KEY_METHOD = "method";
    const CONFIG_ROUTES_KEY_CONTROLLERNAME = "controllerName";
    const CONFIG_ROUTES_KEY_ACTIONNNAME = "actionName";
    const CONFIG_ROUTES_KEY_REQESTATTRIBUTES = "reqestAttributes";
    const CONFIG_CONTROLLER = "controller";
    const CONFIG_CONTROLLER_NAMESPACE = "controller_namespace";
    const CONFIG_CONTROLLER_SUFIX = "controller_suffix";
    const CONFIG_BASE_DIR_VIEWS = "baseDirViews";


    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param Request $request
     * @return RouteMatch|null
     */
    public function route(Request $request): ?RouteMatch
    {
        $requestPath = $request->getPath();
        foreach ($this->config as $item) {
            if (!($item[self::CONFIG_ROUTES_KEY_METHOD] === $request->getMethod())) {
                continue;
            }

            if (preg_match($this->getRegex
                ($item[self::CONFIG_ROUTES_KEY_PATH],$item[self::CONFIG_ROUTES_KEY_REQESTATTRIBUTES]),
                $requestPath,
                $matches)
            ) {
                $requestAttributes = array();
                foreach ($matches as $key => $value) {
                    if (!is_numeric($key)) {
                        $requestAttributes[$key] = $value;
                    }
                }
                foreach ($request->getAllParameter() as $key => $value) {
                    $requestAttributes[$key] = $value;
                }

                return new RouteMatch
                (
                    $item[self::CONFIG_ROUTES_KEY_METHOD],
                    $item[self::CONFIG_ROUTES_KEY_CONTROLLERNAME],
                    $item[self::CONFIG_ROUTES_KEY_ACTIONNNAME],
                    $requestAttributes
                );
            }
        }

        return null;
    }

    /**
     * @param string $path
     * @param array $attribute
     * @return string
     */
    private function getRegex(string $path, array $attribute): string
    {
        foreach ($attribute as $key => $value) {
            $pattern = "{" . $key . "}";
            $replace = "(?<" . $key . ">" . $value . ")";
            $path = str_replace($pattern, $replace, $path);
        }
        $string = str_replace("/", "\/", $path);
        $string = "/^" . $string . "$/";

        return $string;
    }
}