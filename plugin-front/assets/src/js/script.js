/**
 * SASS
 */
import '../sass/layout.scss'

/**
 * JavaScript
 */
import axios from 'axios'

// TODO: Save results in localStorage, get them on page refresh, add "clear" btn

const calculator = document.querySelector('.ngc-calculator')
const form = calculator.querySelector('.ngc-calculator__form')
const formLastChild = form.children.item(form.children.length - 1)

const elements = {
    'productName': form.querySelector('input[name="product-name"]'),
    'netAmount': form.querySelector('input[name="net-amount"]'),
    'currency': form.querySelector('select[name="currency"]'),
    'vatRate': form.querySelector('select[name="vat-rate"]'),
    'submitBtn': form.querySelector('.ngc-calculator__button')
}

/**
 * Validation Functions
 */
const setTwoPointsAfterDot = (value) => {
    value = value.toString()

    if (!value.includes('.')) {
        value += '.00'
    } else {
        let parts = value.split('.')

        if (parts[1].length === 1) {
            value += '0'
        }
        if (parts[1].length > 2) {
            value = parseFloat(value).toFixed(2)
        }
    }

    return value.toString()
}

const validateAndFormatNumber = (value, fieldName) => {  
    let stringValue = value.toString()

    if (typeof stringValue === 'string' && stringValue.length === 0) {
        throw new Error(`${fieldName} must be filled.`)
    }

    stringValue = stringValue.replace(',', '.')

    // remove all whitespace chars
    stringValue = stringValue.replace(/\s+/g, '')

    const numberValue = parseFloat(stringValue)
    if (isNaN(numberValue)) {
        throw new Error(`${fieldName} must be a valid number.`)
    }

    if (numberValue < 0.0) {
        throw new Error(`${fieldName} has to be a positive value.`)
    }

    return setTwoPointsAfterDot(value)
}

const validateCalculationFormData = () => {
    const errors = []

    if (typeof elements.productName.value !== 'string' || elements.productName.value.trim() === '') {
        errors.push('Error: Product name must be a non-empty string.')
    }

    try {
        elements.netAmount.value = validateAndFormatNumber(elements.netAmount.value, 'Net amount')
    } catch (error) {
        errors.push(error)
    }

    if (typeof elements.currency.value !== 'string' || elements.currency.value.trim() === '') {
        errors.push('Error: Currency must be a non-empty string.')
    }

    try {
        validateAndFormatNumber(elements.vatRate.value, 'VAT rate')
    } catch (error) {
        errors.push(error)
    }

    if (errors.length > 0) {
        return {
            valid: false,
            errors: errors
        }
    }

    const data = {}
    Object.keys(elements).forEach(key => {
        data[key] = elements[key].value
    })

    return {
        valid: true,
        data
    }
}

/**
 * API Functions
 */
const calculate = async () => {
    const oldResultWrapper = calculator.querySelector('.ngc-calculator__result')
    if (oldResultWrapper) {
        oldResultWrapper.remove()
    }

    const data = getValidData()
    if (!data) return

    setLoadingState(true)

    try {
        const response = await axios.post('/wp-json/net-gross-calc/v1/calculate', data)
        fillFormWithResponse(response.data.result)
    } catch (error) {
        console.error(`Error while calculating: ${error.message}`)
    } finally {
        setLoadingState(false)
    }
}

/**
 * UI Functions
 */
const setLoadingState = (isLoading) => {
    elements.submitBtn.disabled = isLoading
    // TODO: Get translations from PHP wp_localize()
    elements.submitBtn.textContent = isLoading ? 'Loading...' : 'Calculate again'
}

const fillFormWithResponse = (result) => {
    const resultTextElement = document.createElement('p')
    resultTextElement.classList.add('ngc-calculator__result-text')
    resultTextElement.textContent = `Gross price for product: "${elements.productName.value}", is: ${result.grossAmount} ${elements.currency.value}, including VAT: ${result.vatAmount} ${elements.currency.value}.`
    
    const newResultWrapper = document.createElement('div')
    newResultWrapper.classList.add('ngc-calculator__result')
    
    newResultWrapper.appendChild(resultTextElement)
    form.insertBefore(newResultWrapper, formLastChild)

    elements.submitBtn.textContent = 'Calculate again'
}

const displayErrors = (errors) => {
    const errorContainer = document.createElement('div')
    errorContainer.classList.add('ngc-calculator__error-messages', 'mt-2')
    
    errors.forEach(error => {
        const errorMessage = document.createElement('p')
        errorMessage.classList.add('text-red-500')
        errorMessage.textContent = error
        errorContainer.appendChild(errorMessage)
    })

    form.insertBefore(errorContainer, formLastChild)
}

const getValidData = () => {
    const validationResult = validateCalculationFormData()

    const errorContainer = form.querySelector('.ngc-calculator__error-messages')
    if (errorContainer) {
        errorContainer.remove()
    }

    if (!validationResult.valid) {
        displayErrors(validationResult.errors)
        return null
    }

    return validationResult.data
}

/**
 * Event handlers
 */
const allowNumbersAndComma = (value) => {       
    const firstRegex = /[^0-9.,]/g
    value = value.replace(firstRegex, '')
    
    const secondRegex = /^[.,]/
    if (secondRegex.test(value)) {
        value = value.replace(secondRegex, '')
    }
    
    return value
}

const handleForm = () => {
    elements.submitBtn.addEventListener('click', calculate)

    elements.netAmount.addEventListener('input', (e) => {
        e.target.value = allowNumbersAndComma(e.target.value)
    })
}

/**
 * Initialization
 */
if (form) {
    handleForm()
} else {
    console.error("Form not found! Make sure the form element exists in the DOM.")
}
