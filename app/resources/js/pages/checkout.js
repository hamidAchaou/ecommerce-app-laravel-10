export default function checkout() {
    return {
        countries: window.countries || [],

        selectedCountry: window.oldCountryId || "",
        selectedCity: window.oldCityId || "",

        get filteredCities() {
            const country = this.countries.find(c => c.id == this.selectedCountry);
            return country ? country.cities : [];
        },
    }
}
