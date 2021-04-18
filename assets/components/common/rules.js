const REQUIRED = function() {
    return function(v) {
        return !!v || 'Dit veld is verplicht.';
    }
};
const LIMIT_LENGTH = function(max) {
    return function(v) {
        return typeof v === 'string' && v.length < Number(max) || `Dit veld mag maximaal ${max} karakters lang zijn`;
    }
};

export {REQUIRED, LIMIT_LENGTH}