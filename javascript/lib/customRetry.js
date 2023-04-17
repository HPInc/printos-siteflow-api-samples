const axiosRetry = require('axios-retry');

function isRetryableError(error) {
    return axiosRetry.isNetworkOrIdempotentRequestError(error) || isTooManyRequestsError(error);
}

function exponentialDelay(retryCount, error) {
    const coefficient = isTooManyRequestsError(error) ? (300 * 60) : 100; // assume 429 limit by minute
    const delay = Math.pow(2, retryCount) * coefficient;
    const randomSum = delay * 0.4 * Math.random(); // 0-40% of the delay
    return delay + randomSum;
}

function isTooManyRequestsError(error) {
	return !!(error && error.response && error.response.status === 429);
}

module.exports = { isRetryableError, exponentialDelay, isTooManyRequestsError };
