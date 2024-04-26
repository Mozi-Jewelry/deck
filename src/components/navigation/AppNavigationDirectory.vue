<template>
	<NcAppNavigationItem v-if="directories.length > 0"#
		v-for="directory in directories" :key="directory.id"
		:name="directory.name"
		:to="to"
		:exact="true"
		:allow-collapse="collapsible"
		:open="opened">
	</NcAppNavigationItem>
</template>

<script>
import AppNavigationBoard from './AppNavigationBoard.vue'
import { NcAppNavigationItem } from '@nextcloud/vue'

export default {
	name: 'AppNavigationDirectory',
	components: {
		NcAppNavigationItem,
		AppNavigationBoard,
	},
	props: {
		to: {
			type: String,
			default: '',
		},
		id: {
			type: String,
			required: true,
		},
		directories: {
			type: Array,
			required: true,
		},
		/**
		 * Control whether the category should be opened when adding boards.
		 * This is for example used in the case a new board has been added, so the user directly sees it.
		 */
		openOnAddBoards: {
			type: Boolean,
			default: false,
		},
		defaultOpen: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			opened: false,
		}
	},
	computed: {
		boardsSorted() {
			return [...this.boards].sort((a, b) => a.title.localeCompare(b.title))
		},
		collapsible() {
			return this.boards.length > 0
		},
	},
	watch: {
		boards(newVal, prevVal) {
			if (this.openOnAddBoards === true && prevVal.length < newVal.length) {
				this.opened = true
			}
		},
	},
	mounted() {
		this.opened = this.defaultOpen
		console.log('Mount Directories')
	},
}
</script>
