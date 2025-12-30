defmodule PhoenixApiWeb.UserJSON do
  alias PhoenixApi.Accounts.User

  def index(%{users: users}) do
    %{data: Enum.map(users, &user_json/1)}
  end

  def show(%{user: user}) do
    %{data: user_json(user)}
  end

  defp user_json(%User{} = user) do
    %{
      id: user.id,
      first_name: user.first_name,
      last_name: user.last_name,
      gender: user.gender,
      birthdate: user.birthdate
    }
  end
end