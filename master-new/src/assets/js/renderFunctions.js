export const renderPrice = (price) => {
    return price.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ') + ' руб.';
};

export const renderDate = (dateString, longMonth = false) => {
    const date = new Date(dateString);
    const options = { day: 'numeric', month: (longMonth ? 'long' : 'numeric'), year: 'numeric' };
    return date.toLocaleDateString('ru-RU', options);
}

export const getStatusBadgeClass = (status) => ({
    created: 'bg-warning',
    payed: 'bg-success',
    declined: 'bg-danger'
}[status] || '');

export const renderExpiredDate = (dateString) => {
    const inputDate = new Date(dateString);
    inputDate.setHours(0, 0, 0, 0);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return inputDate < today;
};

// ...