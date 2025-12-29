// api-helper.js
class SliderAPI {
    constructor(baseUrl = '') {
        this.baseUrl = baseUrl;
    }

    // Get all sliders
    async getSliders() {
        try {
            const response = await fetch(`${this.baseUrl}api-image-slider.php?action=get_sliders`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            return await response.json();
        } catch (error) {
            console.error('Error fetching sliders:', error);
            return { success: false, error: 'Network error' };
        }
    }

    // Get single slider by ID
    async getSlider(id) {
        try {
            const response = await fetch(`${this.baseUrl}api-image-slider.php?action=get_slider&id=${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            return await response.json();
        } catch (error) {
            console.error('Error fetching slider:', error);
            return { success: false, error: 'Network error' };
        }
    }

    // Add new slider
    async addSlider(formData) {
        try {
            const response = await fetch(`${this.baseUrl}api-image-slider.php?action=add_slider`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            return await response.json();
        } catch (error) {
            console.error('Error adding slider:', error);
            return { success: false, error: 'Network error' };
        }
    }

    // Update slider
    async updateSlider(id, formData) {
        try {
            formData.append('id', id);
            const response = await fetch(`${this.baseUrl}api-image-slider.php?action=update_slider`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            return await response.json();
        } catch (error) {
            console.error('Error updating slider:', error);
            return { success: false, error: 'Network error' };
        }
    }

    // Delete slider
    async deleteSlider(id) {
        try {
            const response = await fetch(`${this.baseUrl}api-image-slider.php?action=delete_slider`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id: id })
            });
            return await response.json();
        } catch (error) {
            console.error('Error deleting slider:', error);
            return { success: false, error: 'Network error' };
        }
    }

    // Upload image only
    async uploadImage(file) {
        try {
            const formData = new FormData();
            formData.append('image', file);
            
            const response = await fetch(`${this.baseUrl}api-image-slider.php?action=upload_image`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            return await response.json();
        } catch (error) {
            console.error('Error uploading image:', error);
            return { success: false, error: 'Network error' };
        }
    }
}

// Usage example:
// const api = new SliderAPI();
// api.getSliders().then(data => console.log(data));