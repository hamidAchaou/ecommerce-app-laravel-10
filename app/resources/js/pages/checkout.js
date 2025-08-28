export default function checkout() {
    return {
        // قائمة الدول + المدن (Laravel يحولها لـ JSON من الـ Controller)
        countries: window.countries || [],

        // قيم مختارة
        selectedCountry: window.oldCountryId || "",
        selectedCity: window.oldCityId || "",

        // Cities filtered حسب البلد
        get filteredCities() {
            const country = this.countries.find(c => c.id == this.selectedCountry);
            return country ? country.cities : [];
        },

        init() {
            // Debugging console (تشوف لو كلشي واصل صح)
            console.log("Checkout initialized");
            console.log("Countries:", this.countries);
            console.log("Selected Country:", this.selectedCountry);
            console.log("Selected City:", this.selectedCity);
        }
    }
}
