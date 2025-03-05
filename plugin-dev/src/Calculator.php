<?php
namespace NetGrossCalc;
use Timber\Timber;

class Calculator
{
    private static $availableVatRates = [
        ['label' => '0%', 'value' => '0'],
        ['label' => '3%', 'value' => '0.03'],
        ['label' => '5%', 'value' => '0.05'],
        ['label' => '7%', 'value' => '0.07'],
        ['label' => '8%', 'value' => '0.08'],
        ['label' => '22%', 'value' => '0.22'],
        ['label' => '23%', 'value' => '0.23'],
        ['label' => 'zw.', 'value' => '0'],
        ['label' => 'np.', 'value' => '0'],
        ['label' => 'oo.', 'value' => '0']
    ];

    private static $availableCurrencies = [
        'PLN',
        // More in future...
    ];

    /**
     * Renders the net-gross calculator form.
     *
     * The function retrieves the available VAT rates and currencies from the Calculator class and passes them to the 'calculator.twig' template.
     * The 'calculator.twig' template is then compiled with the context containing the available VAT rates and currencies.
     *
     * @return string The compiled 'calculator.twig' template with the context containing the available VAT rates and currencies.
     */
    public static function render()
    {
        $context = Timber::context();
        $context['available_vat_rates'] = static::$availableVatRates;
        $context['available_currencies'] = static::$availableCurrencies;

        return Timber::compile('calculator.twig', $context);
    }

    /**
     * Registers a shortcode to display the net-gross calculator form.
     *
     * The shortcode [ngc_calc_form] is used to display the calculator form on a WordPress page.
     * When the shortcode is rendered, it calls the static method 'render' of the Calculator class.
     *
     * @return void
     */
    public static function registerShortcode()
    {
        add_shortcode('ngc_calc_form', [__CLASS__, 'render']);
    }

    /**
     * Performs the calculation and saves the results in a custom post type.
     *
     * @param array $params The input parameters used in the calculation.
     * @return array The calculated gross amount and VAT amount.
     * @throws \Exception If any error occurs while saving the results.
     */
    public static function calculate($params)
    {
        //* Params:
        // 'productName' => string,
        // 'netAmount' => double,
        // 'currency' => string,
        // 'vatRate' => double,

        self::validate($params);

        $vatAmount = 0.0;
        if ($params['vatRate'] > 0.00) {
            $vatAmount = $params['netAmount'] * $params['vatRate'];
        }

        $grossAmount = $params['netAmount'] + $vatAmount;

        self::setTwoPointsAfterDot($grossAmount);
        self::setTwoPointsAfterDot($vatAmount);

        try {
            self::saveResultsInCpt($params, $grossAmount, $vatAmount);
        }
        catch (\Exception $e) {
            throw 'Error saving results in CPT: ' . $e->getMessage();
        }

        return [
            'grossAmount' => (string)$grossAmount,
            'vatAmount' => (string)$vatAmount
        ];
    }

    /**
     * Saves the calculation results in a custom post type.
     *
     * @param array $params The input parameters used in the calculation.
     * @param float $grossAmount The calculated gross amount.
     * @param float $vatAmount The calculated VAT amount.
     * @throws \Exception If any error occurs while saving the results.
     */
    public static function saveResultsInCpt($params, $grossAmount, $vatAmount)
    {
        $postData = [
            'post_title'   => sanitize_text_field($params['productName']),
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'calculations',
        ];

        $postId = wp_insert_post($postData);

        if (is_wp_error($postId) || $postId === 0) {
            wp_delete_post($postId);
            throw new \Exception('Failed to create post: ' . $postId->get_error_message());
        }

        $netAmountUpdated = update_field('net_amount', $params['netAmount'], $postId);
        $currencyUpdated = update_field('currency', $params['currency'], $postId);
        $vatRateUpdated = update_field('vat_rate', $params['vatRate'], $postId);
        $grossAmountUpdated = update_field('gross_amount', $grossAmount, $postId);
        $vatAmountUpdated = update_field('vat_amount', $vatAmount, $postId);
        $userIpUpdated = update_field('user_ip', self::getUserIp(), $postId);
        $calcDateUpdated = update_field('calc_date', self::getCurrentDate(), $postId);

        if (!$netAmountUpdated || !$currencyUpdated || !$vatRateUpdated || !$grossAmountUpdated || !$vatAmountUpdated || !$userIpUpdated || !$calcDateUpdated) {
            wp_delete_post($postId);
            throw new \Exception('Failed to update post fields.');
        }

        return true;
    }

    /**
     * Validates the input parameters.
     *
     * @param array &$params The input parameters to be validated.
     * @throws \Exception If any of the input parameters are invalid.
     */
    private static function validate(&$params)
    {
        $errors = [];

        //* productName
        if (
            empty($params['productName'])
            || !is_string($params['productName'])
        ) {
            $errors[] = 'Product name must be a non-empty string.';
        }

        //* netAmount
        $params['netAmount'] = self::validateAndFormatNumber($params['netAmount'], 'Net amount', $errors);

        //* currency
        if (empty($params['currency']) || !is_string($params['currency'])) {
            $errors[] = 'Currency must be a non-empty string.';
        }

        //* vatRate
        $params['vatRate'] = self::validateAndFormatNumber($params['vatRate'], 'VAT rate', $errors);

        if (!empty($errors)) {
            throw new \Exception(implode(' ', $errors));
        }
    }

    /**
     * Validates and formats a number.
     *
     * @param string $value The value to be validated and formatted.
     * @param string $fieldName The name of the field being validated.
     * @param array &$errors The array to store any errors.
     * @return float|null The formatted number if it is valid, null otherwise.
     */
    private static function validateAndFormatNumber($value, $fieldName, &$errors)
    {
        if (!is_string($value) || trim($value) === '') {
            $errors[] = "{$fieldName} must be a non-empty string.";
            return null;
        }

        $value = str_replace(',', '.', str_replace(' ', '', $value));

        $numberValue = filter_var($value, FILTER_VALIDATE_FLOAT);
        if ($numberValue === false) {
            $errors[] = "{$fieldName} must be a valid number.";
            return null;
        }

        if ($numberValue < 0.0) {
            $errors[] = "{$fieldName} have to be a positive value.";
            return null;
        }

        self::setTwoPointsAfterDot($value);

        return $value;
    }

    /**
     * Sets the value to have exactly two decimal places.
     *
     * If the value does not contain a decimal point, '.00' is appended.
     * If the value contains a decimal point with one digit after it, '0' is appended.
     * If the value contains a decimal point with more than two digits after it, rounds to two decimal places.
     *
     * @param float &$value The value to be formatted.
     */
    private static function setTwoPointsAfterDot(&$value) {
        $value = (string)$value;

        if (!strpos($value, '.')) {
            $value .= '.00';
        } else {
            $parts = explode('.', $value);
            if (strlen($parts[1]) === 1) {
                $value .= '0';
            }
            if (strlen($parts[1]) > 2) {
                $value = number_format((float)$value, 2, '.', '');
            }
        }

        $value = (float)$value;
    }

    private static function getUserIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // shared service
            return sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // proxy
            return sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
        }
        else {
            // remote
            return sanitize_text_field($_SERVER['REMOTE_ADDR']);
        }
    }

    private static function getCurrentDate()
    {
        return date('d-m-Y H:i:s');
    }
}
