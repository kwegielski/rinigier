defmodule PhoenixApi.Import.UserImporter do
  alias PhoenixApi.Import.CsvParser
  alias PhoenixApi.Accounts.User
  alias PhoenixApi.Repo

  @default_limit 100

  def import, do: import_users(@default_limit)

  def import_users(limit \\ @default_limit) do
    male_names = CsvParser.load_top_first_names("priv/data/male_names.csv")
    female_names = CsvParser.load_top_first_names("priv/data/female_names.csv")

    male_surnames = CsvParser.load_top_last_names("priv/data/male_surnames.csv")
    female_surnames = CsvParser.load_top_last_names("priv/data/female_surnames.csv")

    users =
      Enum.map(1..limit, fn _ ->
        gender = Enum.random(["male", "female"])

        first_name =
          if gender == "male", do: Enum.random(male_names), else: Enum.random(female_names)

        last_name =
          if gender == "male", do: Enum.random(male_surnames), else: Enum.random(female_surnames)

        %{
          first_name: first_name,
          last_name: last_name,
          gender: gender,
          birthdate: random_birthdate()
        }
      end)

    insert_users(users)

    users
  end

  defp insert_users(users) do
    now = NaiveDateTime.utc_now() |> NaiveDateTime.truncate(:second)

    users =
      Enum.map(users, fn user ->
        Map.merge(user, %{
          inserted_at: now,
          updated_at: now
        })
      end)

    Repo.insert_all(User, users)
  end

  defp random_birthdate do
    start = Date.to_erl(~D[1970-01-01]) |> :calendar.date_to_gregorian_days()
    finish = Date.to_erl(~D[2024-12-31]) |> :calendar.date_to_gregorian_days()

    random =
      Enum.random(start..finish)
      |> :calendar.gregorian_days_to_date()

    Date.from_erl!(random)
  end
end