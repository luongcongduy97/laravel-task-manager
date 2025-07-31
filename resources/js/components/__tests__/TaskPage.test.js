import { mount, flushPromises } from '@vue/test-utils';
import { describe, it, expect, vi } from 'vitest';
import TaskPage from '../TaskPage.vue';
import axios from 'axios';

vi.mock('axios');
vi.mock('vue-router', () => ({
  useRoute: () => ({ params: { projectId: 1 } }),
  useRouter: () => ({ push: vi.fn() })
}));
vi.mock('../../auth.js', () => ({
  isAuthenticated: { value: true }
}));

describe('TaskPage', () => {
  it('creates task', async () => {
    axios.get.mockResolvedValueOnce({ data: [] });
    axios.post.mockResolvedValue({ data: { id: 1, name: 'T1', status: 'pending', priority: 'high', due_date: '2025-01-01' } });

    const wrapper = mount(TaskPage);
    await flushPromises();

    await wrapper.find('input[placeholder="Name"]').setValue('T1');
    await wrapper.find('select').setValue('pending');
    await wrapper.find('input[placeholder="Priority"]').setValue('high');
    await wrapper.find('input[placeholder="Due Date"]').setValue('2025-01-01');
    await wrapper.find('button').trigger('click');

    expect(axios.post).toHaveBeenCalledWith('/api/projects/1/tasks', {
      name: 'T1',
      description: '',
      status: 'pending',
      priority: 'high',
      due_date: '2025-01-01',
    });
  });

  it('deletes task', async () => {
    axios.get.mockResolvedValueOnce({ data: [{ id: 1, name: 'T1', status: 'pending', priority: 'high', due_date: '2025-01-01' }] });
    axios.delete.mockResolvedValue({});

    const wrapper = mount(TaskPage);
    await flushPromises();

    await wrapper.find('[data-test="delete-task"]').trigger('click');
    expect(axios.delete).toHaveBeenCalledWith('/api/tasks/1');
  });

  it('shows error when name missing', async () => {
    axios.get.mockResolvedValueOnce({ data: [] });
    const wrapper = mount(TaskPage);
    await flushPromises();

    await wrapper.find('button').trigger('click');
    expect(wrapper.text()).toContain('Name is required');
  });
});