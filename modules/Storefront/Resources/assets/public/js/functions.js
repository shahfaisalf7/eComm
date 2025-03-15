export function trans(langKey, replace = {}) {
    let line = window.FleetCart.langs[langKey];

    for (let key in replace) {
        line = line.replace(`:${key}`, replace[key]);
    }

    return line;
}

export function formatCurrency(amount) {
    return new Intl.NumberFormat(FleetCart.locale.replace("_", "-"), {
        ...(FleetCart.locale === "ar" && {
            numberingSystem: "arab",
        }),
        style: "currency",
        currency: FleetCart.currency,
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount);
}
