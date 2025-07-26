import { mount, flushPromises } from '@vue/test-utils';
import { describe, it, expect, vi } from 'vitest';
import ProjectPage from '../ProjectPage.vue';
import axios from 'axios';

vi.mock('axios');
vi.mock('vue-router', () => ({
  useRoute: () => ({ params: { teamId: 1 } }),
  useRouter: () => ({ push: vi.fn() })
}));
vi.mock('../../auth.js', () => ({
  isAuthenticated: { value: true }
}));

describe('ProjectPage', () => {
  it('creates project', async () => {
    axios.get.mockResolvedValueOnce({ data: [] });
    axios.post.mockResolvedValue({ data: { id: 1, name: 'New', description: '' } });

    const wrapper = mount(ProjectPage);
    await flushPromises();

    await wrapper.find('input[placeholder="Name"]').setValue('New');
    await wrapper.find('button').trigger('click');

    expect(axios.post).toHaveBeenCalledWith('/api/teams/1/projects', { name: 'New', description: '' });
  });

  it('deletes project', async () => {
    axios.get.mockResolvedValueOnce({ data: [{ id: 1, name: 'P1' }] });
    axios.delete.mockResolvedValue({});

    const wrapper = mount(ProjectPage);
    await flushPromises();

    await wrapper.find('[data-test="delete-project"]').trigger('click');
    expect(axios.delete).toHaveBeenCalledWith('/api/projects/1');
  });
});