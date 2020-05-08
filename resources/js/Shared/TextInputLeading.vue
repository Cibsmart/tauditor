<template>
    <div>
        <label v-if="label" :for="id" class="mb-2 block select-none text-gray-800">
            {{ label }}:
        </label>

        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="text-gray-500 line-through">
                {{ leading_character}}
              </span>
            </div>

            <input :id="id" :type="type" v-bind="$attrs" :value="value" ref="input"
                   :class="{ error: errors.length }" @input="$emit('input', $event.target.value)"
                   class="form-input block w-full pl-10 sm:text-sm sm:leading-5 focus:outline-none focus:border-indigo-500 focus:shadow">
        </div>

        <div v-if="errors.length" class="text-red-800 mt-2 text-sm">
            {{ errors[0] }}
        </div>
    </div>
</template>

<script>

    export default{
        inheritAttrs: false,
        props: {
            value: String,
            label: String,
            leading_character: String,
            type: { type: String, default: 'text' },
            errors: { type: Array, default: () => [] },
            id: { type: String, default() {
                    return `text-input-${this._uid}` },
            }
        },

        methods: {

        },
    }
</script>
