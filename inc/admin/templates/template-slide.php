<template id="layer-template">
    <div class="slide_layer"><button class="button button-secondary remove_layer" @click="removeLayer(key,item)">Remove Layer</button>
        <div v-if="item.type == 'image'">
            <div>
                <div class="preview" v-if="item.imgurl != ''">
                    <img src="{{ item.imgurl }}" width="100" />
                </div>
                <label for="image_url">Image</label>
                <input type="text" id="image_url" class="image_url regular-text" v-model="item.imgurl">
                <input type="button" id="upload-btn" class="button-secondary upload-btn" value="Upload Image" @click="mediaPopup(key,item)">
            </div>
            <table>
                <tr>
                    <td><input type="checkbox" v-model="item.width" value="100%"></td>
                    <td> Fill the Width</td>
                </tr>
                <tr>
                    <td><input type="checkbox" v-model="item.height" value="100%"></td>
                    <td> Fill the Height</td>
                </tr>
            </table>
        </div>

        <div v-if="item.type == 'text'">
            <div class="layer-settings">
                <table>
                    <tr>
                        <td><label>Text :</label></td>
                        <td>
                            <textarea v-model="item.content" cols="30" rows="3"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Font size : </label></td>
                        <td><input type="number" v-model="item.settings['font-size']"/></td>
                    </tr>
                    <tr>
                        <td><label>Font weight : </label></td>
                        <td>
                            <select v-model="item.settings['font-weight']">
                                <option v-for=" font_weight in font_weight_opt" value="{{ font_weight }}"> {{ font_weight }} </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Font style : </label></td>
                        <td>
                            <select v-model="item.settings['font-style']">
                                <option v-for="style in font_style_opt" value="{{ style }}">{{ style }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Font color : </label></td>
                        <td><input type="color" v-model="item.settings.color"/></td>
                    </tr>
                    <tr>
                        <td><label>Font Background Color : </label></td>
                        <td><input type="color" v-model="item.settings['background-color']"/></td>
                    </tr>
                </table>
            </div>
        </div>
        <table v-if="item.type != 'Select layer type'">
            <tr>
                <td><label>position : </label></td>
                <td>x : <input type="number" v-model="item.final_pos.left.val"/>
                    <select v-model="item.final_pos.left.unit">
                        <option value="px">PX</option>
                        <option value="%">%</option>
                    </select>
                    y : <input type="number" v-model="item.final_pos.top.val"/>
                    <select v-model="item.final_pos.top.unit">
                        <option value="px">PX</option>
                        <option value="%">%</option>
                    </select>

                </td>
            </tr>
            <tr>
                <td> Show animation : </td>
                <td>
                    <select id="animaton" v-model="item.show">
                        <option v-for="anim in animations" value="{{ anim }}">{{ anim }}</option></select>
                </td>
            </tr>
            <tr>
                <td> Hide animation : </td>
                <td>
                    <select id="animaton" v-model="item.hide">
                        <option v-for="anim in animations" value="{{ anim }}">{{ anim }}</option></select>
                </td>
            </tr>
            <tr>
                <td>Delay : </td>
                <td><input type="number" v-model="item.delayTime" @change="setDelay(item)"/></td>
            </tr>

        </table>
    </div><!--slide layer-->
</template>