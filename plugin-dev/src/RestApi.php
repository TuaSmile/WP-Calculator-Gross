<?php
namespace NetGrossCalc;
use WP_REST_Request, WP_REST_Response;

class RestApi
{
    /** 
     * Registers REST API routes for the NetGrossCalc plugin. 
     * 
     * This function adds a POST route to the 'net-gross-calc/v1' namespace at '/calculate'. 
     * The route is handled by the 'handleCalculation' method of the same class. 
     * 
     * @return void 
     */ 
    public static function registerRoutes()
    {
        add_action('rest_api_init', function () {
            register_rest_route('net-gross-calc/v1', '/calculate', [
                'methods' => 'POST',
                'callback' => [__CLASS__, 'handleCalculation'],
            ]);
        });
    }


    /**
     * Handles the calculation request from the REST API.
     *
     * This function validates the input parameters, performs the calculation using the Calculator class,
     * and returns the result or an appropriate error message.
     *
     * @param WP_REST_Request $request The request object containing the input parameters.
     *
     * @return WP_REST_Response The response object containing the result or an error message.
     *
     * @throws Exception If an error occurs during the calculation.
     */
    public static function handleCalculation(WP_REST_Request $request)
    {
        $params = [
            'productName' => $request->get_param('productName'),
            'netAmount' => $request->get_param('netAmount'),
            'currency' => $request->get_param('currency'),
            'vatRate' => $request->get_param('vatRate'),
        ];

        //* Wanted data types:
        // 'productName' => string,
        // 'netAmount' => double,
        // 'currency' => string,
        // 'vatRate' => double

        try {
            foreach ($params as $key => $value) {
                if ($value === null) {
                    return new WP_REST_Response([
                        'message' => "'{$key}' is required."
                    ], 400);
                }
                if (
                    ($key == 'netAmount' || $key == 'vatRate')
                    && $value < 0.0
                    ) {
                    return new WP_REST_Response([
                        'message' => "'{$key}' have to be a positive value."
                    ], 400);
                }
            }

            $result = Calculator::calculate($params);

            return new WP_REST_Response([
                'message' => 'Calculated successfully.',
                'result' => $result
            ], 200);
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            error_log('Error doing calculation: ' . $message);

            return new WP_REST_Response([
                'message' => $message ?: 'Internal server error.'
            ], 500);
        }
    }
}
