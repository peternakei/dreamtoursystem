<script setup lang="ts">
import {watch,onBeforeUnmount} from 'vue'
import {useEditor,EditorContent} from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import {Button} from '@/components/ui/button'
const props=defineProps<{modelValue:string;disabled?:boolean;label?:string}>(),emit=defineEmits(['update:modelValue'])
const editor=useEditor({extensions:[StarterKit,Link.configure({openOnClick:false})],content:props.modelValue,editable:!props.disabled,editorProps:{attributes:{role:'textbox','aria-label':props.label||'Formatted text','aria-multiline':'true',class:'min-h-40 p-3 outline-none'}},onUpdate:({editor})=>emit('update:modelValue',editor.getHTML())})
watch(()=>props.modelValue,value=>{if(editor.value&&editor.value.getHTML()!==value)editor.value.commands.setContent(value||'',false)})
watch(()=>props.disabled,value=>editor.value?.setEditable(!value))
onBeforeUnmount(()=>editor.value?.destroy())
function link(){const url=window.prompt('Link address',editor.value?.getAttributes('link').href||'');if(url===null)return;if(!url){editor.value?.chain().focus().unsetLink().run();return}if(/^https?:\/\//i.test(url))editor.value?.chain().focus().setLink({href:url}).run()}
</script>
<template><div class="rounded border bg-card text-card-foreground"><div v-if="editor" class="flex flex-wrap gap-1 border-b p-2"><Button type="button" size="sm" variant="outline" :disabled="disabled" @click="editor.chain().focus().toggleBold().run()">Bold</Button><Button type="button" size="sm" variant="outline" :disabled="disabled" @click="editor.chain().focus().toggleItalic().run()">Italic</Button><Button type="button" size="sm" variant="outline" :disabled="disabled" @click="editor.chain().focus().toggleHeading({level:2}).run()">Heading</Button><Button type="button" size="sm" variant="outline" :disabled="disabled" @click="editor.chain().focus().toggleBulletList().run()">List</Button><Button type="button" size="sm" variant="outline" :disabled="disabled" @click="link">Link</Button><Button type="button" size="sm" variant="outline" :disabled="disabled" @click="editor.chain().focus().undo().run()">Undo</Button></div><EditorContent :editor="editor" class="rich-text"/></div></template>
<style scoped>.rich-text :deep(ul){list-style:disc;padding-left:1.5rem}.rich-text :deep(ol){list-style:decimal;padding-left:1.5rem}.rich-text :deep(h2){font-size:1.3rem;font-weight:600}.rich-text :deep(p){margin-bottom:.5rem}.rich-text :deep(a){text-decoration:underline}</style>
