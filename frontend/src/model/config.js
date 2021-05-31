let apiUrl = null;
// Url
if (!process.env.NODE_ENV || process.env.NODE_ENV === 'development') {
    apiUrl = "http://localhost:8080/Israel/public/api";
} else {
    apiUrl = "https://parvaty.me/api";
}

export default apiUrl
