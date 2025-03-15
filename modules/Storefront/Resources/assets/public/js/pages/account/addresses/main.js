import Errors from "../../../components/Errors";

Alpine.data(
    "Addresses",
    ({ initialAddresses, initialDefaultAddress, countries }) => ({
        addresses: initialAddresses,
        defaultAddress: initialDefaultAddress,
        countries,
        formOpen: false,
        editing: false,
        loading: false,
        form: { state: "" },
        states: {},
        cities: {},
        zones: {},
        errors: new Errors(),

        get firstCountry() {
            return Object.keys(this.countries)[0];
        },

        get hasAddress() {
            return Object.keys(this.addresses).length !== 0;
        },

        get hasNoStates() {
            return Object.keys(this.states).length === 0;
        },

        get hasNoCities() {
            return Object.keys(this.cities).length === 0;
        },

        get hasNoZones() {
            return Object.keys(this.zones).length === 0;
        },

        init() {
            this.changeCountry(this.firstCountry);
        },

        changeDefaultAddress(address) {
            if (this.defaultAddress.address_id === address.id) return;

            this.defaultAddress.address_id = address.id;

            axios
                .post(route("account.addresses.change_default"), {
                    address_id: address.id,
                })
                .then((response) => {
                    notify(response.data);
                })
                .catch((error) => {
                    notify(error.response.data.message);
                });
        },

        changeCountry(country) {
            this.form.country = country;
            this.form.state = "";

            this.fetchStates(country);
        },

        changeState(state) {
            this.form.state = state;
            this.form.city = "";
            this.form.zone = "";

            this.fetchCities(state);
        },

        changeCity(city) {
            this.form.city = city;
            this.form.zone = "";

            this.fetchZones(city);
        },

        async fetchStates(country, address = '') {
            const response = await axios.get(
                route("countries.states.index", { code: country })
            );

            this.states = response.data;

            if (address) {
                $('#state option').each(function() {
                    if ($(this).text() === address.state) {
                      $(this).prop('selected', true);
                    }
                });
                var state_id = $('#state').val();
                await this.fetchCities(state_id, address);
            }
        },

        async fetchCities(state, address = '') {
            const response = await axios.get(
                route("states.cities.index", { code: state })
            );

            this.cities = response.data;

            setTimeout(() => {
                if (address) {
                    $('#city option').each(function() {
                        if ($(this).text() === address.city) {
                          $(this).prop('selected', true);
                        }
                    });
                    var city_id = $('#city').val();
                    this.fetchZones(city_id, address);
                }
            }, 1000);
        },

        async fetchZones(city, address = '') {
            const response = await axios.get(
                route("cities.zones.index", { code: city })
            );

            this.zones = response.data;

            setTimeout(() => {
                if (address) {
                    $('#zone option').each(function() {
                        if ($(this).text() === address.zone) {
                          $(this).prop('selected', true);
                        }
                    });
                }
            }, 1000);
        },

        edit(address) {
            this.formOpen = true;
            this.editing = true;
            this.form = address;

            this.fetchStates(address.country, address);
        },

        remove(address) {
            if (!confirm(trans("storefront::account.addresses.confirm"))) {
                return;
            }

            axios
                .delete(route("account.addresses.destroy", address.id))
                .then((response) => {
                    delete this.addresses[address.id];

                    notify(response.data.message);
                })
                .catch((error) => {
                    notify(error.response.data.message);
                });
        },

        cancel() {
            this.editing = false;
            this.formOpen = false;

            this.errors.reset();
            this.resetForm();
        },

        save() {
            this.loading = true;

            this.editing ? this.update() : this.create();
        },

        update() {
            var zone_id = $('#zone').val();
            this.form.zone = zone_id;
            axios
                .put(
                    route("account.addresses.update", { id: this.form.id }),
                    this.form
                )
                .then(({ data }) => {
                    this.formOpen = false;
                    this.editing = false;

                    this.addresses[this.form.id] = data.address;

                    this.resetForm();

                    notify(data.message);
                })
                .catch(({ response }) => {
                    if (response.status === 422) {
                        this.errors.record(response.data.errors);
                    }

                    notify(response.data.message);
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        create() {
            axios
                .post(route("account.addresses.store"), this.form)
                .then(({ data }) => {
                    this.formOpen = false;

                    let address = { [data.address.id]: data.address };

                    this.addresses = {
                        ...this.addresses,
                        ...address,
                    };

                    this.resetForm();

                    notify(data.message);
                })
                .catch(({ response }) => {
                    if (response.status === 422) {
                        this.errors.record(response.data.errors);
                    }

                    notify(response.data.message);
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        resetForm() {
            this.form = { state: "" };
        },
    })
);
