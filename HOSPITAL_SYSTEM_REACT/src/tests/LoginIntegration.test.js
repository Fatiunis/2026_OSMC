import { render, fireEvent, waitFor, screen } from '@testing-library/react';
import { BrowserRouter } from 'react-router-dom';
import LoginPage from '../pages/portalG/LoginPage';

describe('Login Integration Test', () => {
  beforeEach(() => {
    global.fetch = jest.fn();
    sessionStorage.clear();
  });

  it('should successfully login with valid credentials', async () => {
    global.fetch.mockResolvedValueOnce({
      ok: true,
      json: () => Promise.resolve({
        success: true,
        usuario: { id: 1, correo: 'test@example.com', rol: 'admin' }
      })
    });

    render(
      <BrowserRouter>
        <LoginPage />
      </BrowserRouter>
    );

    fireEvent.change(screen.getByLabelText(/correo/i), {
      target: { value: 'test@example.com' }
    });
    fireEvent.change(screen.getByLabelText(/contraseña/i), {
      target: { value: '123456' }
    });

    fireEvent.click(screen.getByRole('button', { name: /iniciar sesión/i }));

    await waitFor(() => {
      expect(window.sessionStorage.getItem('rol')).toBe('admin');
    });
  });
});