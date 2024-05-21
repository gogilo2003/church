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

export const prepDate = (value: string) => {
    let dt = new Date(value)
    let year = dt.getFullYear()
    let month: string | number = dt.getMonth() + 1

    if (month.toString().length < 2) {
        month = '0'.concat(month.toString())
    }

    let date: string | number = dt.getDate()

    if (date.toString().length < 2) {
        date = '0'.concat(date.toString())
    }

    return `${year}-${month}-${date}`
}
