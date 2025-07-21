import { mount, flushPromises } from '@vue/test-utils';
import { describe, it, expect, vi } from 'vitest';
import TeamPage from '../TeamPage.vue';
import axios from 'axios';

vi.mock('axios');
vi.mock('vue-router', () => ({
  useRoute: () => ({ params: { teamId: 1 } }),
  useRouter: () => ({ push: vi.fn() })
}));
vi.mock('../../auth.js', () => ({
  isAuthenticated: { value: true }
}));

describe('TeamPage', () => {
  it('sends invite', async () => {
    axios.get
      .mockResolvedValueOnce({ data: { role: 'Admin' } })
      .mockResolvedValueOnce({ data: [{ id: 1, name: 'Team 1' }] });
    axios.post.mockResolvedValue({ data: { email: 'invite@example.com' } });

    const wrapper = mount(TeamPage);
    await flushPromises();

    await wrapper.find('input[placeholder="Name"]').setValue('Invitee');
    await wrapper.find('input[placeholder="Email"]').setValue('invite@example.com');
    await wrapper.find('form').trigger('submit.prevent');

    expect(axios.post).toHaveBeenCalledWith('/api/teams/1/invite', {
      name: 'Invitee',
      email: 'invite@example.com'
    });
  });

  it('deletes team', async () => {
    axios.get
      .mockResolvedValueOnce({ data: { role: 'Admin' } })
      .mockResolvedValueOnce({ data: [{ id: 1, name: 'Team 1' }] });
    axios.delete.mockResolvedValue({});

    const wrapper = mount(TeamPage);
    await flushPromises();

    await wrapper.find('[data-test="delete-team"]').trigger('click');

    expect(axios.delete).toHaveBeenCalledWith('/api/teams/1');
  });
});