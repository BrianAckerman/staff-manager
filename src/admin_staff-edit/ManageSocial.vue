<template>
    <div class="social-links">
        <div v-if="links.length < 1 || this.view">
            <form @submit.prevent>
                <div>
                    <label for="link-type">Type</label>
                    <select name="link-type" v-model="activeLink.type">
                        <option>- Select -</option>
                        <option value="Facebook">Facebook</option>
                        <option value="Twitter">Twitter</option>
                        <option value="LinkedIn">LinkedIn</option>
                        <option value="YouTube">YouTube</option>
                    </select>
                </div>
                <div>
                    <label for="link-url">Link</label>
                    <input type="url" name="link-url" v-model="activeLink.url" />
                </div>
                <div>
                    <button @click.prevent="save">Save</button>
                </div>
            </form>
        </div>
        <div v-else>
            <ul>
                <li v-for="(link, index) in links" :key="index">
                    <a :href="link.url" target="_blank">{{ link.type }}</a>
                    <button @click.prevent="edit(index)">edit</button>
                    <button @click.prevent="remove(index)">delete</button>
                </li>
            </ul>
            <button @click.prevent="this.new">New</button>
        </div>
        <ul>
            <li v-for="(error, index) in errors" :key="index">{{ error.message }}</li>
        </ul>
    </div>
</template>
<script>
export default {
    data: function () {
        return {
            view: false,
            activeIndex: null,
            activeLink: {
                type: "",
                url: "",
            },
            links: [
                {
                    type: "Facebook",
                    url: "http://www.facebook.com",
                },
            ],
            errors: [],
        };
    },
    methods: {
        new() {
            this.activeLink = {
                type: "",
                url: "",
            };
            this.activeIndex = this.links.length + 1;
            this.view = !this.view;
        },
        edit(index) {
            this.activeIndex = index;
            this.activeLink = this.links[index];
            this.view = !this.view;
        },
        save() {
            if (this.activeLink.type === "") {
                // need to check if this error is already in the errors set
                this.errors.push({
                    type: "error:type",
                    message: "You must select a social network",
                });
                return;
            }
            if (this.activeLink.url === "") {
                // need to check if this error is already in the errors set
                this.errors.push({
                    type: "error:url",
                    message: "Please enter a url in the field",
                });
                return;
            }
            if (this.links[this.activeIndex] && this.activeIndex) {
                this.links[this.activeIndex].type = this.activeLink.type;
                this.links[this.activeIndex].url = this.activeLink.url;
            } else {
                this.links.push({
                    type: this.activeLink.type,
                    url: this.activeLink.url,
                });
            }
            this.view = !this.view;
            this.errors = [];
        },
        remove(index) {
            console.log(index);
            this.links.splice(index, 1);
        },
    },
};
</script>
<style scoped>
.invalid {
    border-color: red;
}

.valid {
    border-color: green;
}

h3 {
    margin-top: 0;
}

.social-links div {
    margin-bottom: 1rem;
    display: flex;
    flex-direction: column;
}

.social-links div:last-child {
    display: flex;
    justify-content: center;
}

.social-links label {
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.social-links input,
.social-links select {
    font-size: 1rem;
    padding: 0.5rem;
    border-radius: 0.25rem;
    border: 1px solid #ccc;
}

.social-links button {
    padding: 0.5rem;
    border-radius: 0.25rem;
    border: none;
    background-color: #007cba;
    color: #fff;
}

.social-links button:hover {
    background-color: #006aa5;
    cursor: pointer;
}
</style>