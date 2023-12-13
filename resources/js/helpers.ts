export const formatDate = (date: string) => {
    let dt = new Date(date);

    return dt.toLocaleDateString('en-KE', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

export const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-KE', { style: 'currency', currency: 'KES' }).format(value)
}
