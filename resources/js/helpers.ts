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


export const getWeekOfYear = (param) => {

    let date = new Date(param)

    // Copy date so that the original date is not modified
    const currentDate = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));

    // Set to nearest Thursday: currentDate becomes currentDate + 4 - currentDate.getDay() (from Sun=0 to Sat=6)
    currentDate.setUTCDate(currentDate.getUTCDate() + 4 - (currentDate.getUTCDay() || 7));

    // Get first day of the year
    const yearStart = new Date(Date.UTC(currentDate.getUTCFullYear(), 0, 1));

    // Calculate full weeks to nearest Thursday
    const weekNumber = Math.ceil((((currentDate - yearStart) / 86400000) + 1) / 7);

    return weekNumber;
}
