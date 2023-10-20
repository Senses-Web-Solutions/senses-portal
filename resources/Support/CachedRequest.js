import axios from 'axios';

// Caches a request for the user's current browser session.
// Useful for tooltips.

export default {
    get(url, params) {
        return new Promise((resolve, reject) => {
            if (this.cache[url]) {
                resolve(this.cache[url]);
                return;
            }
            axios
                .get(url, params)
                .then((response) => {
                    this.cache[url] = response;
                    resolve(response);
                })
                .catch((error) => {
                    if (this.cache[url]) {
                        delete this.cache[url];
                    }
                    reject(error);
                });
        });
    },
    flush(url = null) {
        if (url) {
            if (this.cache[url]) {
                delete this.cache[url];
            }
        } else {
            this.cache = {};
        }
        return this;
    },
    fresh() {
        this.flush();
        return this;
    },
    cache: {},
};
